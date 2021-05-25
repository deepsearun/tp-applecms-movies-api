<?php


namespace app\validate;

/**
 * Class SearchValidate
 * @package app\validate
 * @author Xiejiawei<forceboot@hotmail.com>
 */
class SearchValidate extends BaseValidate
{
    protected $rule = [
        'keyword' => 'require',
        'sort' => 'alphaDash',
        'page' => 'number|>:0',
        'pageSize' => 'number|>:0'
    ];
}