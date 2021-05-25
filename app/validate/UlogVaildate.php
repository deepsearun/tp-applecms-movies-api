<?php


namespace app\validate;

/**
 * Class UlogVaildate
 * @package app\validate
 * @author Xiejiawei<forceboot@hotmail.com>
 */
class UlogVaildate extends BaseValidate
{
    protected $rule = [
        'ulog_id' => 'require|number|>:0|isUlogExist',
        'ulog_ids' => 'require|isUlogExist',
        'ulog_type' => 'require|alphaDash',
        'ulog_rid' => 'require|number|>:0',
        'ulog_sid' => 'require|number',
        'ulog_nid' => 'require|number',
        'second' => 'require|number',
        'duration' => 'require|number',
        'type' => 'alphaDash',
        'page' => 'number|>:0',
        'pageSize' => 'number|>:0',
    ];

    protected $scene = [
        'list' => ['type', 'page', 'pageSize'],
        'createVod' => ['ulog_type', 'ulog_rid', 'ulog_sid', 'ulog_nid'],
        'createArt' => ['ulog_rid'],
        'createTopic' => ['ulog_rid'],
        'delete' => ['ulog_id'],
        'deletes' => ['ulog_ids'],
        'updateVodProgress' => ['ulog_rid', 'second']
    ];
}