<?php

namespace app\validate;

/**
 * Class VodValidate
 * @package app\validate
 * @author Xiejiawei<forceboot@hotmail.com>
 */
class VodValidate extends BaseValidate
{
    protected $rule = [
        'class_id' => 'number',
        'class_type' => 'alphaDash',
        'time' => 'require|alphaDash',
        'page' => 'number|>:0',
        'pageSize' => 'number|>:0',
        'vod_id' => 'require|number|>:0|isVodExist'
    ];

    protected $scene = [
        'today' => ['class_type', 'class_id','page', 'pageSize'],
        'list' => ['class_type', 'class_id','page', 'pageSize'],
        'slideByClassId' => ['class_type','class_id'],
        'hits' => ['time', 'class_type', 'class_id', 'page', 'pageSize'],
        'detail' => ['vod_id'],
        'support' => ['vod_id'],
        'collect' => ['vod_id']
    ];
}