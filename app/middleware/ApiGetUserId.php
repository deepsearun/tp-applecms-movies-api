<?php


namespace app\middleware;

/**
 * Class ApiGetUserId
 * @package app\middleware
 * @author Xiejiawei<forceboot@hotmail.com>
 */
class ApiGetUserId
{
    /**
     * 获取用户userId
     * @param object $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, \Closure $next)
    {
        // 获取头部信息
        $param = $request->header();
        if (array_key_exists('authorization', $param)) {
            if ($user = cache($param['authorization'])) {
                $request->userId = $user['user_id'];
            }
        }
        return $next($request);
    }
}