<?php

namespace app\model;

use app\lib\exception\BaseException;

class Art extends Base
{
    protected $pk = 'art_id';

    protected $hidden = ['art_tpl', 'art_time_add', 'art_jumpurl'];

    protected $globalScope = ['status'];

    /**
     * 定义全局的查询范围
     * @param $query
     */
    public function scopeStatus($query)
    {
        $query->where('art_status', 1);
    }

    /**
     * 获取资讯列表
     * @param array $where
     * @param array $options
     * @return array
     * @throws BaseException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function list(array $where = [], array $options = []): array
    {
        if (empty($where)) $where = $this->isByClassParentIdOrSonId();
        $sort = isset($options['sort']) ? $options['sort'] : 'art_time desc';
        $page = intval(input('page', 1));
        $pageSize = intval(input('pageSize', 12));
        $total = $this->where($where)->count();
        $list = $this->where($where)
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
        return $this->whereIn('art_id', $in)->select()->toArray();
    }

    /**
     * 获取热门资讯
     * @return array
     * @throws \app\lib\exception\BaseException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getHits(): array
    {
        return $this->list([], ['sort' => $this->createHitsWhere('art') . ' desc']);
    }

    /**
     * 搜索关键词联想
     * @return array
     * @throws BaseException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function searchThink()
    {
        return $this->search('art_id,art_name',false);
    }

    /**
     * 搜索文章
     * @param string $field
     * @param bool $addSearchLog
     * @return array
     * @throws \app\lib\exception\BaseException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function search($field = '*',$addSearchLog = true): array
    {
        $keyword = $this->createSearchWords($addSearchLog);
        $page = intval(input('page', 1));
        $pageSize = intval(input('pageSize', 10));
        $total = $this->where('art_name', 'like', '%' . $keyword . '%')
            ->count();
        $data = $this->where('art_name', 'like', '%' . $keyword . '%')
            ->field($field)
            ->page($page, $pageSize)
            ->order($this->sortByDesc('art'))
            ->select()->toArray();
        foreach ($data as $key => $row) {
            $data[$key]['art_name'] = keywordReplace($row['art_name'], $keyword);
        }
        return $this->showResArr($data, $total, $page, $pageSize);
    }

    /**
     * 获取资讯详情
     * @return array
     * @throws BaseException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function detail(): array
    {
        $art_id = intval(input('art_id'));
        $data = $this->find($art_id)->toArray();
        $data['vod'] = [];
        $data['art'] = [];
        // 文章下包含的影片
        if (!empty($data['art_rel_vod'])) {
            $data['vod'] = (new Vod())->listByIn($data['art_rel_vod']);
        }
        // 文章下包含的文章
        if (!empty($data['topic_rel_art'])) {
            $data['art'] = (new Art())->listByIn($data['art_rel_art']);
        }
        // 统计资讯热度
        $this->countHits('art', $art_id);
        // 如果用户已登录 创建浏览记录
        if (request()->userId) (new Ulog())->addArtVisit($art_id);
        return $this->showResArr($data);
    }
}