<?php

namespace app\validate;

/**
 * Class TopicValidate
 * @package app\validate
 * @author Xiejiawei<forceboot@hotmail.com>
 */
class TopicValidate extends BaseValidate
{
    protected $rule = [
        'time' => 'require|alphaDash',
        'page' => 'number|>:0',
        'pageSize' => 'number|>:0',
        'topic_id' => 'require|number|>:0|isTopicExist'
    ];

    protected $scene = [
        'list' => ['page', 'pageSize'],
        'hits' => ['time', 'page', 'pageSize'],
        'detail' => ['topic_id']
    ];
}