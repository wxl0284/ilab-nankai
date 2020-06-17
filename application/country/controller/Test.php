<?php
namespace app\country\controller;
use think\Response;
use think\Db;
use country_api\sdk\IlabJwt;

class Test 
{
    public function test ()
    {
        return IlabJwt::TYPE_SYS;
    }
}