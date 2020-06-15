<?php
namespace app\index\controller;
use think\Response;
use think\Db;
use think\Session;
use think\Cookie;
/**
 * 用户模块控制器
 * Class Subject
 * @package app\index\controller
 */
class User extends Common
{
//    public $user_id;
//
//    public function __construct()
//    {
//        parent::__construct();
//        $info = Session::get('home_info');
//        $this->user_id = Session::get('home_user_id');
//        if(empty($info) || empty($this->user_id)){
//            return $this->redirect('login/index');
//        }
//        $this->assign('info', $info);
//    }

    public function index(){
        return $this->fetch();
    }

    // 修改姓名
    public function updateName(){
        Db::name('user')->where(array('id' => $this->user_id))->update(array('name' => $this->request->post('name')));
        return json(array('code' => 0, 'msg' => '修改成功'));
    }

    // 修改性别
    public function updateSex(){
        $sex = $this->request->post('gender') == "F" ? "女" : "男";
        Db::name('user')->where(array('id' => $this->user_id))->update(array('sex' => $sex));
        return json(array('code' => 0, 'msg' => '修改成功'));
    }

    //注册
    public function register(){
        $region = Db::name("region")->select();
        $this->assign("region",$region);
        $industry = Db::name("industry")->select();
        $this->assign("industry",$industry);
        $school = Db::name("school")->select();
        $this->assign("school",$school);
        return $this->fetch();
    }

    //登陆
//    public function login(){
//        return $this->fetch();
//    }

    //学校
    /*public function school(){
        $get = $this->request->get();
        $school = Db::name("school")->where(['region_id'=>$get['province']])->select();
        return json(['code'=>200,'result'=>$school]);
    }

    public function register_up(){
        $date = $this->request->post();
        if(empty($date['phone']) || !is_mobile($date['phone'])){
            return json(['code'=>100,'msg'=>"请输入正确的手机号！"]);
        }
        if(empty($date['code'])){
            return json(['code'=>100,'msg'=>"请输入验证码！"]);
        }
        if(empty($date['name'])){
            return json(['code'=>100,'msg'=>"请输入昵称！"]);
        }
        if(empty($date['email']) || !is_email($date['email'])){
            return json(['code'=>100,'msg'=>"请输入正确的邮箱！"]);
        }
        if(!$date['role']){
            return json(['code'=>100,'msg'=>"请选择身份！"]);
        }
        if(($date['code'] == 2 || $date['role'] == 3) && !$date['school']){
            return json(['code'=>100,'msg'=>"请选择学校！"]);
        }
        if($date['code'] == 4 && !$date['industry']){
            return json(['code'=>100,'msg'=>"请选择行业！"]);
        }
        if(strlen($date['password']) < 6 || strlen($date['password']) > 64){
            return json(['code'=>100,'msg'=>"请输入正确的密码（6-64位）！"]);
        }
        if(strlen($date['password2']) < 6 || strlen($date['password2']) > 64){
            return json(['code'=>100,'msg'=>"请输入正确的确认密码"]);
        }
        if($date['password'] != $date['password2']){
            return json(['code'=>100,'msg'=>"两次输入的密码不一致！"]);
        }
        if(Db::name("user")->where(['phone'=>$date['phone']])->find()){
            return json(['code'=>100,'msg'=>"该手机号已被注册过！"]);
        }
        if(Db::name("user")->where(['email'=>$date['email']])->find()){
            return json(['code'=>100,'msg'=>"该邮箱已被注册过！"]);
        }
        $data = array(
            "name" => $date['name'],
            "province" => $date['province'],
            "role" => $date['role'],
            "school" => $date['school'],
            "unit" => "",
            "password" => password_hash_tp($date['password']),
            "industry" => $date['industry'],
            "phone" => $date['phone'],
            "email" => $date['email'],
            "create_time" => time()
        );
        $user = Db::name("user")->insertGetId($data);
        session::set('user_id',$user);
        session::set('phone',$data['phone']);
        return json(['code'=>200,'msg'=>"注册成功！"]);
    }*/

//    public function login_up(){
//        $date = $this->request->post();
//        $info = Db::name("user")->where(['phone'=>$date['phone']])->whereOr(['name'=>$date['phone']])->whereOr(['email'=>$date['phone']])->where(['password' => password_hash_tp($date['password'])])->find();
//        if($info){
//            session::set('home_user_id',$info['id']);
//            session::set('home_info',$info);
//            return json(['code'=>200,'msg'=>"登陆成功！"]);
//        }
//        return json(['code'=>500,'msg'=>"登陆失败！"]);
//    }

    /**
     * 修改密码
     */
    public function change_password(){
//        if($this->request->isPost()){
//            $info = Db::name('user')->where(array('id' => $this->user_id))->find();
//            $data = $this->request->param();
//            if($info['password'] != password_hash_tp($data['oldPassword'])){
//                return json(array('code' => 1, 'msg' => '原密码错误'));
//            }
//            Db::name('user')->where(array('id' => $this->user_id))->update(array('password' => password_hash_tp($data['password'])));
//            return json(array('code' => 0, 'msg' => '修改成功'));
//        }
        return $this->fetch();
    }

    /**
     * 我的收藏
     */
    public function my_collection(){
        if($this->request->isPost()){
            Db::name('subject_collect')->where(array('id' => $this->request->param('id')))->update(array('is_delete' => 1));
            return json(array('code' => 0, 'msg' => '修改成功'));
        }
        $limit = $this->request->param('limit', 10);
        $page = $this->request->param('page', 0);
        $count = Db::name('subject_collect')
            ->alias('sc')
            ->join('subject s', 'sc.subject_id=s.id', 'LEFT')
            ->where(array('sc.is_delete' => 0))
            ->where(array('sc.user_id' => $this->user_id))
            ->where('s.id', 'not null')
            ->count();
        $list = Db::name('subject_collect')
            ->alias('sc')
            ->join('subject s', 'sc.subject_id=s.id', 'LEFT')
            ->field('sc.*,s.subject_name,s.id as sub_id,s.equip_pic')
            ->where(array('sc.is_delete' => 0))
            ->where(array('sc.user_id' => $this->user_id))
            ->where('s.id', 'not null')
            ->limit($page, $limit)
            ->select();
        $this->assign('page', $page+1);
        $this->assign('count', $count);
        $this->assign('list', $list);
        return $this->fetch();
    }

    /**
     * 历史评价
     */
    public function my_evaluate(){
        if($this->request->isAjax()){
            Db::name('subject_collect')->where(array('id' => $this->request->param('id')))->update(array('is_delete' => 1));
            return json(array('code' => 0, 'msg' => '修改成功'));
        }
        $limit = $this->request->param('limit', 10);
        $page = $this->request->param('page', 0);
        $count = Db::name('subject_evaluate')
            ->alias('sc')
            ->join('subject s', 'sc.subject_id=s.id', 'LEFT')
            ->where(array('sc.user_id' => $this->user_id))
            ->where('s.id', 'not null')
            ->count();
        $list = Db::name('subject_evaluate')
            ->alias('sc')
            ->join('subject s', 'sc.subject_id=s.id', 'LEFT')
            ->field('sc.*,s.subject_name,s.id as sub_id,s.equip_pic')
            ->where(array('sc.user_id' => $this->user_id))
            ->where('s.id', 'not null')
            ->limit($page, $limit)
            ->select();
        $this->assign('page', $page+1);
        $this->assign('count', $count);
        $this->assign('list', $list);
        return $this->fetch();
    }

    /**
     * 我的项目
     */
    public function project_learn(){
        $list = Db::name('subject_examine')
            ->alias('se')
            ->join('subject s', 'se.subject_id=s.id', 'LEFT')
            ->field('se.*,s.subject_name,s.person_charge,s.subject_brief,s.equip_pic')
            ->where(array('user_id' => $this->user_id))
            ->where(array('is_delete' => '0'))
            ->select();
        $this->assign('list', $list);
        return $this->fetch();
    }

    /**
     * 搜索我的项目
     */
    public function search(){
        $name = $this->request->param('name');
        $where = [];
        if($name != ''){
            $where['s.subject_name'] = ['like', '%'.$name.'%'];
        }
        $list = Db::name('subject_examine')
            ->alias('se')
            ->join('subject s', 'se.subject_id=s.id', 'LEFT')
            ->field('se.*,s.subject_name,s.person_charge,s.subject_brief')
            ->where(array('user_id' => $this->user_id))
            ->where(array('is_delete' => '0'))
            ->where($where)
            ->select();
        $count = Db::name('subject_examine')
            ->alias('se')
            ->join('subject s', 'se.subject_id=s.id', 'LEFT')
            ->field('se.*,s.subject_name,s.person_charge,s.subject_brief')
            ->where(array('user_id' => $this->user_id))
            ->where(array('is_delete' => '0'))
            ->where($where)
            ->count();
        return json(array('data' => $list, 'meta' => array('size' => '1', 'start' => '1', 'total' => $count)));
    }

//    /**
//     * 预约通知
//     */
//    public function order_notice(){
//        return $this->fetch();
//    }
//
//    /**
//     * 我的预约
//     */
//    public function my_expOrder(){
//        return $this->fetch();
//    }

//    public function logout(){
//        Session::set('user_id', '');
//        Session::set('home_info', '');
//        return $this->fetch('login');
//    }
}
