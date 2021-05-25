<?php

use \app\lib\exception\ExceptionHandler;
use app\Request;

// 容器Provider定义文件
return [
    'think\Request' => Request::class,
    // 绑定自定义错误处理
    'think\exception\Handle' => ExceptionHandler::class,
];
