<?php

namespace app\model;

/**
 * Class Topic
 * @package app\model
 * @author Xiejiawei<forceboot@hotmail.com>
 */
class Topic extends Base
{
    protected $pk = 'topic_id';

    protected $hidden = ['topic_tpl', 'topic_time_add'];

    protected $globalScope = ['status'];

    /**
     * 定义全局的查询范围
     * @param $query
     */
    public function scopeStatus($query)
    {
        $query->where('topic_status', 1);
    }

    /**
     * 获取专题列表
     * @param array $where
     * @param array $options
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function list(array $where = [], array $options = []): array
    {
        $sort = isset($options['sort']) ? $options['sort'] : 'topic_time desc';
        $page = intval(input('page', 1));
        $pageSize = intval(input('pageSize', 10));
        $total = $this->where($where)->count();
        $list = $this->where($where)
            ->page($page, $pageSize)
            ->order($sort)
            ->select()
            ->toArray();
        return $this->showResArr($list, $total, $page, $pageSize);
    }

    /**
     * 获取热门专题
     * @return array
     * @throws \app\lib\exception\BaseException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getHits(): array
    {
        return $this->list([], ['sort' => $this->createHitsWhere('topic') . ' desc']);
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
        return $this->search('topic_id,topic_name',true);
    }

    /**
     * 搜索专题
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
        $total = $this->where('topic_name', 'like', '%' . $keyword . '%')
            ->count();
        $data = $this->where('topic_name', 'like', '%' . $keyword . '%')
            ->field($field)
            ->page($page, $pageSize)
            ->order($this->sortByDesc('topic'))
            ->select()->toArray();
        foreach ($data as $key => $row) {
            $data[$key]['topic_name'] = keywordReplace($row['topic_name'], $keyword);
        }
        return $this->showResArr($data, $total, $page, $pageSize);
    }

    /**
     * 获取专题详情
     * @return array
     * @throws \app\lib\exception\BaseException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function detail(): array
    {
        $topic_id = intval(input('topic_id'));
        $data = $this->find($topic_id)->toArray();
        $data['vod'] = [];
        $data['art'] = [];
        // 专题下包含的影片
        if (!empty($data['topic_rel_vod'])) {
            $data['vod'] = (new Vod())->listByIn($data['topic_rel_vod']);
        }
        // 专题下包含的文章
        if (!empty($data['topic_rel_art'])) {
            $data['art'] = (new Art())->listByIn($data['topic_rel_art']);
        }
        // 统计专题热度
        $this->countHits('topic', $topic_id);
        // 如果用户已登录 创建浏览记录
        if (request()->userId) (new Ulog())->addTopicVisit($topic_id);
        return $this->showResArr($data);
    }
}