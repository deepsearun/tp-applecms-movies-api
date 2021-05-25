<?php


namespace app\model;

use think\facade\Cache;
use think\facade\Db;
use think\Model;

/**
 * Class Base
 * @package app\model
 * @author Xiejiawei<forceboot@hotmail.com>
 */
class Base extends Model
{
    /**
     * 列表统一数据格式
     * @param array $list
     * @param int $page
     * @param int $pageSize
     * @param int $total
     * @return array
     */
    public function showResArr(array $list = [], int $total = 0, int $page = 1, int $pageSize = 0)
    {
        return [
            'list' => $list,
            'total' => $total,
            'page' => $page,
            'pageSize' => $pageSize
        ];
    }

    /**
     * 生成缓存Key
     * @param string $tag
     * @param array $data
     * @return string
     */
    public function createCacheKey($tag = '', $data = [])
    {
        $params = input();
        $key = md5(http_build_query(array_merge($params, $data)));
        return $tag . '_' . $key;
    }

    /**
     * 创建热门搜索条件
     * @param string $key
     * @return string
     * @throws \app\lib\exception\BaseException
     */
    public function createHitsWhere(string $key)
    {
        $params = input();
        $arr = [
            'all' => $key . '_hits',
            'day' => $key . '_hits_day',
            'week' => $key . '_hits_week',
            'month' => $key . '_hits_month'
        ];
        if (!array_key_exists($params['time'], $arr)) ApiException('非法参数', 10000);
        return $arr[$params['time']];
    }

    /**
     * 统计人气 当天 本周 本月 总数
     * @param string $name
     * @param int $id
     * @param int $step
     * @return bool
     * @throws \think\db\exception\DbException
     */
    public function countHits(string $name, int $id, int $step = 1)
    {
        $lock = 'lock_count_' . $name . '_hits_' . $id;
        $step = mt_rand(1, $step * mt_rand(1, 10));
        if (cache($lock)) return false;
        $field = [
            'day' => $name . '_hits_day',
            'week' => $name . '_hits_week',
            'month' => $name . '_hits_month'
        ];
        $setCache = function ($time) use ($id, $field) {
            $expire = 86400;
            $key = $field[$time] . '_' . $id;
            if ($time == 'week') $expire *= 7;
            if ($time == 'month') $expire *= 30;
            return Cache::set($key, 1, $expire);
        };
        $nameId = $name . '_id';
        foreach ($field as $key => $row) {
            if (cache($row . '_' . $id)) { // +1
                Db::name($name)->where($nameId, $id)->inc($row, $step)->update();
            } else { // 不存在 清空点击数 并设置缓存
                Db::name($name)->where($nameId, $id)->update([$row => 0]);
                $setCache($key);
            }
        }
        Db::name($name)->where($nameId, $id)->inc($name . '_hits', $step)->update();
        // 设置锁
        cache($lock, 1, 5);
        return true;
    }

    /**
     * 通过父分类ID或子分类ID查询
     * @return array
     * @throws \app\lib\exception\BaseException
     */
    public function isByClassParentIdOrSonId(): array
    {
        $params = input();
        $arr = ['parent_id' => 'type_id_1', 'son_id' => 'type_id'];
        if (!isset($params['class_type']) || !isset($params['class_id'])) return [];
        if (!array_key_exists($params['class_type'], $arr)) ApiException('非法参数', 10000);
        return [[$arr[$params['class_type']], '=', $params['class_id']]];
    }

    /**
     * 通用列表排序 desc
     * @param string $type
     * @return string
     * @throws \app\lib\exception\BaseException
     */
    public function sortByDesc(string $type): string
    {
        $sort = input('sort', 'time');
        $sortArr = [
            'time' => $type . '_time',
            'hits' => $type . '_hits',
            'score' => $type . '_score',
            'up' => $type . '_up',
        ];
        if (!array_key_exists($sort, $sortArr)) ApiException('非法参数', 10000);
        return $sortArr[$sort] . ' desc';
    }

    /**
     * 创建搜索关键词记录 返回请求关键词
     * @param bool $add
     * @return mixed
     */
    public function createSearchWords($add = true)
    {
        $keyword = trim(urldecode(input('keyword')));
        if ($add) (new Search())->add($keyword);
        return $keyword;
    }

    /**
     * 解析播放和下载链接
     * @param string $str
     * @return array
     */
    public function parsePlayAndDownloadUrl(string $str): array
    {
        $res = [];
        $arr = [];
        if (empty($str)) return [];
        $groupArr = explodeByRule('$$$', $str);
        foreach ($groupArr as $item) {
            $arr[] = explode('#', $item);
        }
        for ($i = 0; $i <= count($arr) - 1; $i++) {
            foreach ($arr[$i] as $row) {
                $arrs = explode('$', $row);
                if (!array_key_exists(0, $arrs) || !array_key_exists(1, $arrs)) continue;
                $res[$i][] = [
                    'title' => '高清',
                    'episode' => $arrs[0] ?? '',
                    'src' => $arrs[1] ?? ''
                ];
            }
        }
        return $res;
    }

    /**
     * 解析播放数据
     * @param $data
     * @return array
     */
    public function parsePlayData(array $data): array
    {
        $playFrom = explodeByRule('$$$', trim($data['vod_play_from'], '$$$'));
        $downFrom = explodeByRule('$$$', trim($data['vod_down_from'], '$$$'));
        $playList = $this->parsePlayAndDownloadUrl(trim($data['vod_play_url'], '$$$'));
        $downList = $this->parsePlayAndDownloadUrl(trim($data['vod_down_url'], '$$$'));
        if (count($playFrom) > count($playList)){
            $playFrom = array_splice($playFrom,1);
        }
        if (empty($downFrom) || empty($downList)) {
            $data['vod_play_from'] = $playFrom;
            $data['vod_play_url'] = $playList;
            unset($playList, $playList);
            return $data;
        }
        $data['vod_down_from'] = $downFrom;
        $data['vod_down_url'] = $downList;
        $data['vod_play_from'] = array_merge($playFrom, $downFrom);
        $data['vod_play_url'] = array_merge($playList, $downList);
        unset($downList, $downFrom, $playList, $playList);
        return $data;
    }

}