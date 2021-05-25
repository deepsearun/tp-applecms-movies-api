<?php

namespace app\lib\socket;

use think\worker\Server;

/**
 * Worker 命令行服务类
 */
class Events extends Server
{
    protected $socket = 'http://0.0.0.0:2346';

    public static function onMessage($connection,$data)
    {
        $connection->send(json_encode($data));
    }
}