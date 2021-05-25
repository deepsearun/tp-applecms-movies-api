<?php

namespace app\middleware;

use \app\model\User;

/**
 * Class ApiUserStatus
 * @package app\middleware
 * @author Xiejiawei<forceboot@hotmail.com>
 */
class ApiUserStatus
{
    /**
     * @param $request
     * @param \Closure $next
     * @return mixed
     * @throws \app\lib\exception\BaseException
     */
    public function handle($request, \Closure $next)
    {
        $param = $request->userTokenUserInfo;
        (new User())->checkStatus($param);
        return $next($request);
    }
}