<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

use think\facade\Route;

Route::miss('v1.Index/miss');

// 需要验证token
Route::group('api/:version', function () {
    // 注销登录
    Route::post('/user/logout', ':version.User/logout');
    // 新增用户使用设备
    Route::post('/device/create', ':version.Device/create');
    // 获取用户影片播放记录
    Route::get('/ulog/vod', ':version.Ulog/userVod');
    // 获取用户文章记录
    Route::get('/ulog/art', ':version.Ulog/userArt');
    // 获取用户专题记录
    Route::get('/ulog/topic', ':version.Ulog/userTopic');
    // 获取用户影片收藏记录
    Route::get('/ulog/vodCollect',':version.Ulog/userVodCollect');
    // 获取用户影片点赞记录
    Route::get('/ulog/vodSupport',':version.Ulog/userVodSupport');
    // 添加影片记录
    Route::post('/ulog/create/vod', ':version.Ulog/createVod');
    // 更新影片播放进度
    Route::post('/ulog/update/vod/progress', ':version.Ulog/updateVodProgress');
    // 删除记录
    Route::get('/ulog/delete/:ulog_id', ':version.Ulog/delete');
    // 删除多条记录
    Route::post('/ulog/deletes', ':version.Ulog/deletes');
})->middleware([\app\middleware\ApiUserAuth::class]);

// 需要验证token 和用户状态
Route::group('api/:version', function () {
    // 获取用户信息
    Route::get('/user/info', ':version.User/info');
    // 修改头像
    Route::post('/user/changeAvatar',':version.User/changeAvatar');
    // 修改资料
    Route::post('/user/changeInfo',':version.User/changeInfo');
    // 点赞影片
    Route::post('/support/vod/:vod_id',':version.Support/vod');
    // 收藏影片
    Route::post('/collect/vod/:vod_id',':version.Collect/vod');
})->middleware([\app\middleware\ApiUserAuth::class, \app\middleware\ApiUserStatus::class]);


