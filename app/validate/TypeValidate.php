<?php

namespace app\validate;

/**
 * Class TypeValidate
 * @package app\validate
 * @author Xiejiawei<forceboot@hotmail.com>
 */
class TypeValidate extends BaseValidate
{
    protected $rule = [
        'parent_id' => 'require|number|>:0',
        'son_id' => 'number|>:0',
        'class' => 'chsAlpha',
        'area' => 'chsAlpha',
        'lang' => 'chsAlpha',
        'year' => 'number',
        'sort' => 'alphaDash',
        'page' => 'number|>:0',
        'pageSize' => 'number|>:0',
    ];

    protected $scene = [
        'screenVodByParentId' => ['parent_id', 'son_id', 'class', 'area', 'lang', 'year', 'page', 'pageSize', 'sort'],
        'screenVodBySonId' => ['son_id', 'class', 'area', 'lang', 'year', 'page', 'pageSize', 'desc']
    ];
}