<?php
use think\facade\Route;

// 不需要token
Route::group('api/:version', function () {
    // 用户登录
    Route::post('/user/login', ':version.User/login');
    // 获取验证码
    Route::post('/user/sendCode', ':version.User/sendCode');
    // 手机号登录
    Route::post('/user/phoneLogin', ':version.User/phoneLogin');
    // 用户注册
    Route::post('user/reg', ':version.User/reg');
    // 今日最新视频
    Route::get('/vod/today', ':version.Vod/today');
    // 获取影片列表
    Route::get('/vod/list', ':version.Vod/list');
    // 幻灯图
    Route::get('/vod/slide', ':version.Vod/slideShow');
    // 获取热度视频
    Route::get('/vod/hits/:time', ':version.Vod/Hits');
    // 获取影片详情
    Route::get('/vod/detail/:vod_id', ':version.Vod/detail')
        ->middleware([\app\middleware\ApiGetUserId::class]);
    // 影片关键词联想
    Route::post('/search/vod/think', ':version.Search/vodThink');
    // 文章关键词联想
    Route::post('/search/art/think', ':version.Search/artThink');
    // 专题关键词联想
    Route::post('/search/topic/think', ':version.Search/topicThink');
    // 搜索影片
    Route::post('/search/vod', ':version.Search/vod')
        ->middleware([\app\middleware\ApiGetUserId::class]);
    // 搜索文章
    Route::post('/search/art', ':version.Search/art');
    // 搜索话题
    Route::post('/search/topic', ':version.Search/topic');
    // 热门搜索列表
    Route::get('/search/hits', ':version.Search/hitsSearch');
    // 获取当前父分类
    Route::get('/class/parent/:type', ':version.Type/getParentClass');
    // 获取当前父分类下的子分类
    Route::get('/class/son/:parent_id', ':version.Type/getSonClassByParent');
    // 通过父分类ID筛选影片
    Route::get('/class/screenVodByParentId/:parent_id', ':version.Type/screenVodByParentId');
    // 通过子分类ID筛选影片
    Route::get('/class/screenVodBySonId/:son_id', ':version.Type/screenVodBySonId');
    // 获取专题列表
    Route::get('/topic/list', ':version.Topic/list');
    // 获取热门专题
    Route::get('/topic/hits/:time', ':version.Topic/hits');
    // 获取专题详情
    Route::get('/topic/detail/:topic_id', ':version.Topic/detail')
        ->middleware([\app\middleware\ApiGetUserId::class]);
    // 获取资讯列表
    Route::get('/art/list', ':version.Art/list');
    // 获取热门资讯
    Route::get('/art/hits/:time', ':version.Art/hits');
    // 获取资讯详情
    Route::get('/art/detail/:art_id', ':version.Art/detail')->middleware([\app\middleware\ApiGetUserId::class]);
});