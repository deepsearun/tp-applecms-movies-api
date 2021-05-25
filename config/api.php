<?php

return [
    //短信配置
    'sms' => [
        //短信厂商 目前只支持ali
        'type' => 'ali',
        'ali' => [
            //是否开启功能 不开启将不发送短信 直接返回验证码
            'isOpen' => false,
            'accessKeyId' => '',
            'accessSecret' => '',
            //签名名称
            'signName' => 'forceboot',
            //短信模版
            'templateCode' => 'SMS_189711506',
            //验证码发送时间间隔
            'expire' => 60,
            //ip日限制发送数量
            'ipLimit' => 1000
        ],
    ]
];