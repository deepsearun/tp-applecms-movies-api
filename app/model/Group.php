<?php

namespace app\model;

class Group extends Base
{
    protected $globalScope = ['status'];

    /**
     * 定义全局的查询范围
     * @param $query
     */
    public function scopeStatus($query)
    {
        $query->where('group_status', 1);
    }
}