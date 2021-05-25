<?php
declare (strict_types=1);

namespace app\model;

/**
 * Class Vod
 * @package app\model
 * @author Xiejiawei<forceboot@hotmail.com>
 */
class Vod extends Base
{
    protected $pk = 'vod_id';

    protected $globalScope = ['status'];

    /**
     * 定义全局的查询范围
     * @param $query
     */
    public function scopeStatus($query)
    {
        $query->where('vod_status', 1);
    }

    /**
     * 关联子分类
     * @return \think\model\relation\HasOne
     */
    public function type()
    {
        return $this->hasOne('Type', 'type_id', 'type_id');
    }

    /**
     * 关联父分类
     * @return \think\model\relation\HasOne
     */
    public function parentType()
    {
        return $this->hasOne('Type', 'type_id', 'type_id_1');
    }

    /**
     * 列表通用 限制返回的字段
     * @return  string
     */
    public function listField(): string
    {
        $field = 'vod_id,type_id,type_id_1,vod_name,vod_class,';
        $field .= 'vod_letter,vod_pic,vod_pic_slide,vod_actor,vod_director,vod_blurb,vod_remarks,';
        $field .= 'vod_hits,vod_hits_day,vod_hits_week,vod_hits_month,';
        $field .= 'vod_area,vod_lang,vod_year,vod_level,vod_score,vod_time,vod_content';
        return $field;
    }

    /**
     * 普通内容 限制返回的字段
     * @return string
     */
    public function baseField(): string
    {
        $field = 'vod_id,type_id,type_id_1,vod_name,vod_pic,vod_pic_slide,vod_actor,vod_director,';
        $field .= 'vod_blurb,vod_hits,vod_area,vod_lang,vod_year,vod_score,vod_remarks,vod_time';
        return $field;
    }

    /**
     * 获取影片数据
     * @param array $where
     * @param array $options
     * @return array
     * @throws \app\lib\exception\BaseException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function list(array $where = [], array $options = []): array
    {
        if (empty($where)) $where = $this->isByClassParentIdOrSonId();
        $expire = isset($options['expire']) ? $options['expire'] : 3600;
        $sort = isset($options['sort']) ? $options['sort'] : 'vod_time desc';
        $field = isset($options['field']) ? $options['field'] : $this->listField();
        $page = intval(input('page', 1));
        $pageSize = intval(input('pageSize', 12));
        $cacheName = $this->createCacheKey('list', array_merge($where, $options));
        $total = $this->where($where)->count();
        $list = $this->field($field)
            ->where($where)
            ->cache($cacheName, $expire)
            ->page($page, $pageSize)
            ->order($sort)
            ->select()
            ->toArray();
        return $this->showResArr($list, $total, $page, $pageSize);
    }

    /**
     * 通过IN条件查找数据
     * @param $in
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function listByIn($in): array
    {
        return $this->field($this->listField())
            ->whereIn('vod_id', $in)
            ->select()
            ->toArray();
    }

    /**
     * 获取热门影片
     * @return array
     * @throws \app\lib\exception\BaseException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getHits(): array
    {
        return $this->list([], ['sort' => $this->createHitsWhere('vod') . ' desc', 'expire' => 600]);
    }

    /**
     * 查询今日的最新影片数据
     * @return array
     * @throws \app\lib\exception\BaseException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getToday(): array
    {
        $today = strtotime(date('Y-m-d', strtotime('today')));
        $where = $this->isByClassParentIdOrSonId();
        $where[] = ['vod_time', 'between', [$today, $today + 86400]];
        return $this->list($where);
    }

    /**
     * 获取推荐9 幻灯图影片数据
     * @return array|mixed
     * @throws \app\lib\exception\BaseException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getSlideShow(): array
    {
        $where = $this->isByClassParentIdOrSonId();
        $where[] = ['vod_level', '=', 9];
        return $this->list($where, ['field' => $this->baseField()]);
    }

    /**
     * 搜索关键词联想
     * @return array
     * @throws \app\lib\exception\BaseException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function searchThink()
    {
        return $this->search('vod_id,type_id,type_id_1,vod_name,vod_class,vod_actor,vod_director', false);
    }

    /**
     * 搜索影片 支持搜索名称，演员，导演
     * @param string $field
     * @param bool $addSearchLog
     * @return array
     * @throws \app\lib\exception\BaseException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function search($field = '', $addSearchLog = true): array
    {
        $keyword = $this->createSearchWords($addSearchLog);
        $page = intval(input('page', 1));
        $pageSize = intval(input('pageSize', 10));
        $total = $this->where('vod_name|vod_actor|vod_director', 'like', '%' . $keyword . '%')
            ->count();
        $data = $this->with(['parentType' => function ($query) {
            $query->field('type_id,type_name');
        }])->field($field ? $field : $this->listField())
            ->where('vod_name|vod_actor|vod_director', 'like', '%' . $keyword . '%')
            ->page($page, $pageSize)
            ->order($this->sortByDesc('vod'))
            ->select()
            ->toArray();
        foreach ($data as $key => $row) {
            if ($userId = request()->userId) {
                $data[$key]['isCollect'] = (new Ulog())->isCollectByRid('vod', $row['vod_id']);
            }
        }
        return $this->showResArr($data, $total, $page, $pageSize);
    }

    /**
     * 关联用户浏览记录
     * @return \think\model\relation\HasMany
     */
    public function ulog()
    {
        return $this->hasMany('ulog', 'ulog_rid', 'vod_id');
    }

    /**
     * 获取影片详情
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function detail(): array
    {
        $vod_id = intval(input('vod_id'));
        $data = $this->with(['parentType' => function ($query) {
            $query->field('type_id,type_name');
        }, 'type' => function ($query) {
            $query->field('type_id,type_name');
        }])->find($vod_id)->toArray();
        $data = $this->parsePlayData($data);
        // 用户已登录 返回用户影片记录
        if ($userId = request()->userId) {
            $ulog = new Ulog();
            $data['vod_log'] = [
                'isSupport' => $ulog->isSupportByRid('vod', $vod_id),
                'isCollect' => $ulog->isCollectByRid('vod', $vod_id),
                'history_play' => $ulog->getFind('vod', $vod_id, 4)
            ];
        }
        // 统计影片热度
        $this->countHits('vod', $vod_id);
        return $this->showResArr($data);
    }

    /**
     * 影片点赞
     * @return bool|void
     * @throws \app\lib\exception\BaseException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function support()
    {
        $vod_id = intval(input('vod_id'));
        return (new Ulog())->support('vod', $vod_id);
    }

    /**
     * 影片收藏
     * @return bool
     * @throws \app\lib\exception\BaseException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function collect()
    {
        $vod_id = intval(input('vod_id'));
        return (new Ulog())->collect('vod', $vod_id);
    }
}
