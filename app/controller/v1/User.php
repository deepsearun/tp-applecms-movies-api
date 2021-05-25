<?php

namespace app\controller\v1;

use app\BaseController;
use app\model\User as UserModel;
use app\validate\UserValidate;

/**
 * Class User
 * @package app\controller\v1
 * @author Xiejiawei<forceboot@hotmail.com>
 */
class User extends BaseController
{

    /**
     * 获取验证码
     * @return \think\response\Json
     * @throws \app\lib\exception\BaseException
     */
    public function sendCode()
    {
        (new UserValidate())->goCheck('sendCode');
        // 发送验证码
        (new UserModel())->sendCode();
        return self::showResCodeWithOutData('发送成功');
    }

    /**
     * 手机号登录
     * @return \think\response\Json
     * @throws \app\lib\exception\BaseException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function phoneLogin()
    {
        (new UserValidate())->gocheck('phoneLogin');
        $token = (new UserModel())->phoneLogin();
        return self::showResCode('登录成功', ['token' => $token]);
    }

    /**
     * 用户登录
     * @return \think\response\Json
     * @throws \app\lib\exception\BaseException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function login()
    {
        (new UserValidate())->gocheck('login');
        $token = (new UserModel())->login();
        return self::showResCode('登录成功', ['token' => $token]);
    }

    /**
     * 用户信息
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function info()
    {
        $data = (new UserModel())->getUserInfo();
        return self::showResCode('获取成功', $data);
    }

    /**
     * 修改头像
     * @return \think\response\Json
     * @throws \app\lib\exception\BaseException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function changeAvatar()
    {
        (new UserValidate())->gocheck('changeAvatar');
        $data = (new UserModel())->changeAvatar();
        return self::showResCode('修改成功', ['url' => $data]);
    }

    /**
     * 修改用户资料
     * @return \think\response\Json
     * @throws \app\lib\exception\BaseException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function changeInfo()
    {
        (new UserValidate())->gocheck('changeInfo');
        (new UserModel())->changeInfo();
        return self::showResCodeWithOutData('修改成功');
    }

    /**
     * 注销登录
     * @return \think\response\Json
     * @throws \app\lib\exception\BaseException
     */
    public function logout()
    {
        (new UserModel())->logout();
        return self::showResCodeWithOutData('注销登录成功');
    }
}