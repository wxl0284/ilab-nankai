<?php
use think\Route;

Route::rule('/api/check_user', 'index/Subject/check_user');//南开统一身份认证 获取师生信息
Route::get('/api/experts_enter', 'index/Subject/experts_enter');//专家登录
