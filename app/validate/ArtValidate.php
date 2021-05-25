<?php


namespace app\validate;

/**
 * Class ArtValidate
 * @package app\validate
 * @author Xiejiawei<forceboot@hotmail.com>
 */
class ArtValidate extends BaseValidate
{
    protected $rule = [
        'class_id' => 'number',
        'class_type' => 'alphaDash',
        'time' => 'require|alphaDash',
        'page' => 'number|>:0',
        'pageSize' => 'number|>:0',
        'art_id' => 'require|number|>:0|isArtExist'
    ];

    protected $scene = [
        'list' => ['class_type', 'class_id', 'page', 'pageSize'],
        'hits' => ['time', 'page', 'pageSize'],
        'detail' => ['art_id']
    ];
}