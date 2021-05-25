<?php


namespace app\model;

/**
 * Class Device
 * @package app\model
 * @author Xiejiawei<forceboot@hotmail.com>
 */
class Device extends Base
{
    protected $autoWriteTimestamp = true;
    protected $createTime = 'create_time';

    /**
     * 新增设备信息
     */
    public function add()
    {
        $params = input();
        $data = [
            'user_id' => request()->userId,
            'model' => $params['model'],
            'system' => $params['system'],
            'platform' => $params['platform'],
            'deviceId' => $params['deviceId'],
            'brand' => $params['brand']
        ];
        return $this->create($data);
    }
}