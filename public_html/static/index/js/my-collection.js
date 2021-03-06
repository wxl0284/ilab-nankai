/**
 * 我的收藏
 */
X.sub("init", function() {

    var user = {};
    var start = 0;
    var c_limit = 6;
    var c_itemList = "/index/user/collection_list";
    var c_itemDetail = "/json/projectCollection";
    var itemDel = "/sjc/api/project/collection/rm";
    
    // 检查当前语言
    function checkLang() {
        if (LANG == "EN") {
            document.title = EN.my.myCollect;
        }
    }
    checkLang();
    
    //用户信息
    X.sub('userLogged', function(evt, respText) {
        user = respText;
        loadComment();
    });

    loadComment();

    /*评论列表*/

    function loadComment() {
        X.get(uri_pipe(c_itemList + '?status=1&start=0&limit=' + c_limit + '&sortby=id&reverse=true'), onGetComment);
    }

    /**
     * 分页显示
     **/
    function gotoPage(evt, page) {
        X.get(uri_pipe(c_itemList + '?status=1&sortby=id&reverse=true&limit=' + c_limit + '&start=' + page.start), onGetComment);
    }

    X.sub('gotoPage', gotoPage);

    function onGetComment(respText) {
        var resp = JSON.parse(respText);
        resp = resp.result;

        resp.meta = resp.meta || {
            total: "0",
            size: "0"
        };
        var res = "";
        if (resp.meta && resp.meta.total === '0') {
            res = "<div class='noitems'>暂无收藏</div>";
        } else {
            var size = parseInt(resp.meta.size);
            for (var i = 0; i < size; ++i) {
                var item = resp.data[i];
                res += '<li data-id="' + item.id + '">';
                res += '<img width="147" height="100" src="' + item.equip_pic + '" onerror="this.src=\'/static/index/pic/img.jpg\'" />';
                res += '<div class="res-ctn">';
                res += '<h1 class="title"><a href="/index/subject/detail?id=' + item.sub_id + '">' + (item.subject_name || "") + '</a></h1>';
                res += '<div class="res-action"><span class="c-date">' + item.create_time + '</span>';
                res += '<span class="c-delete" data-id="'+item.id+'">取消收藏</span>';
                res += '</div></div></li>';
            }
        }


        $("#list").html(res);

        //分页
        resp.limit = c_limit;
        resp.evt = "gotoPage";
        resp.ele = X("pagination", true);
        X.pub('pagination', resp);
        
        var y1=$(".content-wrap #list").height();
        var y2=$(".cN-pagination");
            if(y1!=0){
                y2.css("position","initial")
            }
        
    }

    //取消收藏

    X.sub("cancelCollection", function(evt, id) {
        var show = {};
        if(LANG == "EN"){
            show.msg = EN.my.uncollectionTip;
            show.title = EN.detail.cancelCollect;
            show.okText = EN.public.confirm;
            show.closeText = EN.public.cancel;
        }else{
            show.msg = "是否确认取消收藏？";
            show.title = "取消收藏";
        }
        show.callback = function() {
            X.get(c_itemDetail+"?id="+id,function(res){
                res = JSON.parse(res);
                res.projectID = res.projectID || "";
                var item = {};
                item.id = id;
                item.projectID = res.projectID;
                X.post(itemDel, item, function(respText) { //取消收藏
                    respText = JSON.parse(respText);
                    if (respText.code === 0) {
                        if(LANG == "EN"){
                           X.dialog(EN.detail.cancelCollectSuccess); 
                        }else{
                            X.dialog('取消收藏成功!');
                        }
                        loadComment();
                    } else {
                        X.error(respText.msg);
                    }
    
                });
            });
        };
        X.pub('showDialog', show);
    });
});