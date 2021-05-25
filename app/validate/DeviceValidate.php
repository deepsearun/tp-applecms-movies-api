<?php
namespace app\validate;

/**
 * Class DeviceValidate
 * @package app\validate
 * @author Xiejiawei<forceboot@hotmail.com>
 */
class DeviceValidate extends BaseValidate
{
    protected $rule = [
        'model' => 'require',
        'system' => 'require',
        'platform' => 'require'
    ];
}