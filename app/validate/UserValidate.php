<?php


namespace app\validate;

/**
 * Class UserValidate
 * @package app\validate
 * @author Xiejiawei<forceboot@hotmail.com>
 */
class UserValidate extends BaseValidate
{
    protected $rule = [
        'username' => 'require|alphaDash|min:5|max:25',
        'password' => 'require|alphaDash|min:6',
        'phone' => 'require|mobile',
        'code' => 'require|number|length:4|isRightCode',
        'avatar' => 'file|image',
        'qq' => 'number|max:10',
        'email' => 'email',
        'nickname' => 'max:15'
    ];

    protected $message = [
        'username.require' => '用户名不能为空',
        'username.alphaDash' => '用户名格式不正确',
        'username.min'   => '用户名太短了',
        'username.max'  => '用户名不能超过25个字符',
        'password.alphaDash'  => '密码格式不正确',
        'password.min'  => '密码太短了'
    ];

    protected $scene = [
        'login' => ['username', 'password'],
        'phoneLogin' => ['phone', 'code'],
        'sendCode' => ['phone'],
        'changeAvatar' => ['avatar'],
        'changeInfo' => ['nickname','qq','email']
    ];
}