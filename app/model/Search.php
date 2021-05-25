<?php


namespace app\model;

use think\facade\Cache;

/**
 * Class Search
 * @package app\model
 * @author Xiejiawei<forceboot@hotmail.com>
 */
class Search extends Base
{
    /**
     * 因为没有search表 绑定到影片模型
     * @var string
     */
    protected $name = 'vod';

    public $redis;

    /**
     * Search constructor.
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        parent::__construct($data);
        $this->redis = redis();
    }


    /**
     * 创建搜索记录 已存在 查询次数 +1
     * @param $keyword
     */
    public function add($keyword)
    {
        $key = 'keywordSearch_' . md5($keyword);
        if (!Cache::get($key . 'lock') && Cache::set($key . 'lock', 1, 30)) {
            $this->redis->sAdd('search_set', $key);
            $isSearch = $this->redis->hKeys($key);
            if (!$isSearch) {
                $this->redis->hMset($key, [
                    'word' => $keyword,
                    'num' => 1,
                    'time' => time()
                ]);
            } else {
                $this->redis->hIncrBy($key, 'num', 1);
            }
        }
    }

    /**
     * 获取热门搜索
     * @return array
     */
    public function getHits()
    {
        $arrays = [];
        $searchArr = $this->redis->sMembers('search_set');
        if (empty($searchArr)) return $arrays;
        foreach ($searchArr as $key) {
            $arrays[] = [
                'num' => $this->redis->hGet($key, 'num'),
                'keyword' => $this->redis->hGet($key, 'word'),
                'time' => $this->redis->hGet($key, 'time')
            ];
        }
        $arrays = multiArraySort($arrays, 'num', 'desc');
        $arrays = array_slice($arrays, 0, 15);
        return $this->showResArr($arrays, count($arrays));
    }
}