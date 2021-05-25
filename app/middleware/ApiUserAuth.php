<?php
declare (strict_types=1);

namespace app\middleware;

/**
 * Class ApiUserAuth
 * @package app\middleware
 * @author Xiejiawei<forceboot@hotmail.com>
 */
class ApiUserAuth
{
    /**
     * 用户授权请求
     * @param object $request
     * @param \Closure $next
     * @return mixed
     * @throws \app\lib\exception\BaseException
     */
    public function handle($request, \Closure $next)
    {
        //获取头部信息
        $param = $request->header();
        //不含token
        if (!array_key_exists('authorization', $param)) ApiException('非法token，禁止操作', 20003, 200);
        // 当前用户 是否登录
        $token = $param['authorization'];
        $user = cache($token);
        // 未登录或 已过期
        if (!$user) ApiException('非法token，请重新登录', 20003, 200);
        $request->userToken = $token;
        $request->userId = $user['user_id'];
        $request->userTokenUserInfo = $user;
        $request->groupId = $user['group_id'];
        return $next($request);
    }
}
