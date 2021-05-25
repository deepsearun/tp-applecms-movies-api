<?php

namespace app\model;

use think\facade\Db;

/**
 * Class Ulog
 * @package app\model
 * @author Xiejiawei<forceboot@hotmail.com>
 */
class Ulog extends Base
{
    protected $pk = 'ulog_id';

    protected $autoWriteTimestamp = true;

    protected $createTime = 'ulog_time';

    protected $updateTime = 'update_time';

    /**
     * 关联影片
     * @return \think\model\relation\HasOne
     */
    public function vod()
    {
        return $this->hasOne('vod', 'vod_id', 'ulog_rid');
    }

    /**
     * 关联文章
     * @return \think\model\relation\HasOne
     */
    public function art()
    {
        return $this->hasOne('art', 'art_id', 'ulog_rid');
    }

    /**
     * 关联话题
     * @return \think\model\relation\HasOne
     */
    public function topic()
    {
        return $this->hasOne('topic', 'topic_id', 'ulog_rid');
    }

    /**
     * 获取浏览列表
     * @param string $with
     * @param array $where
     * @param array $options
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function list(string $with, array $where = [], array $options = []): array
    {
        $page = intval(input('page', 1));
        $pageSize = intval(input('pageSize', 10));
        $sort = isset($options['sort']) ? $options['sort'] : 'ulog_time desc';
        $withField = isset($options['withField']) ? $options['withField'] : '*';
        $field = isset($options['field']) ? $options['field'] : '*';
        if (!in_array($with, ['vod', 'topic', 'art'])) return [];
        $total = $this->with([$with])->where($where)->count();
        $data = $this->with([$with => function ($query) use ($withField) {
            $query->field($withField);
        }])->where($where)
            ->field($field)
            ->order($sort)
            ->page($page, $pageSize)
            ->select()
            ->toArray();
        return $this->showResArr($data, $total, $page, $pageSize);
    }

    /**
     * 通过主键删除记录
     * @return bool|void
     * @throws \app\lib\exception\BaseException
     */
    public function deleteById(): bool
    {
        $res = $this->where([
            'ulog_id' => input('ulog_id'),
            'user_id' => request()->userId
        ])->delete();
        return $res ? true : ApiException('删除失败', 40001);
    }

    /**
     * 通过主键批量删除记录
     * @return bool
     * @throws \app\lib\exception\BaseException
     */
    public function deleteByInId(): bool
    {
        $id = input('ulog_ids');
        $res = $this->whereIn('ulog_id', $id)->delete();
        return $res ? true : ApiException('删除失败', 40001);
    }

    /**
     * 添加浏览记录 不会重复创建相同记录
     * @param array $data
     * @return bool|void
     * @throws \app\lib\exception\BaseException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function addVisit(array $data = []): bool
    {
        if ($this->where($data)->find()) {
            $this->where($data)->update([
                'update_time' => time()
            ]);
            return true;
        }
        return $this->create($data) ? true : ApiException('新增失败', 40000);
    }

    /**
     * 添加影片浏览记录
     * @return bool|mixed
     * @throws \app\lib\exception\BaseException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function addVodVisit(): bool
    {
        $params = input();
        $row = $this->getFind('vod', $params['ulog_rid'], $params['ulog_type']);
        if ($row) return $this->updateVod();
        $insert = $this->createWhere('vod', $params['ulog_rid'], $params['ulog_type'], $params['ulog_sid'], $params['ulog_nid']);
        $insert['ulog_nid_name'] = $params['ulog_nid_name'];
        return $this->addVisit($insert);
    }

    /**
     * 更新影片播放记录
     * @return Ulog
     */
    public function updateVod()
    {
        $params = input();
        $where = $this->createWhere('vod', $params['ulog_rid'], $params['ulog_type']);
        return $this->where($where)->update([
            'ulog_nid' => $params['ulog_nid'],
            'ulog_sid' => $params['ulog_sid'],
            'ulog_nid_name' => $params['ulog_nid_name']
        ]);
    }

    /**
     * 更新影片播放进度
     * @return Ulog
     */
    public function updateVodProgress()
    {
        $params = input();
        $where = $this->createWhere('vod', $params['ulog_rid'], 4);
        return $this->where($where)->update([
            'ulog_progress' => $params['second'],
            'ulog_duration' => $params['duration'],
            'update_time' => time()
        ]);
    }

    /**
     * 添加资讯浏览记录
     * @param int $art_id
     * @return bool
     * @throws \app\lib\exception\BaseException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function addArtVisit(int $art_id = 0): bool
    {
        $insert = $this->createWhere('art', input('ulog_rid', $art_id), 1);
        return $this->addVisit($insert);
    }

    /**
     * 通过用户ID获取资讯浏览记录
     * @return array
     * @throws \app\lib\exception\BaseException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getArtVisitByUid()
    {
        $where = $this->createWhere('art', null, 1);
        return $this->list('art', $where);
    }


    /**
     * 添加专题浏览记录
     * @param int $topic_id
     * @return bool
     * @throws \app\lib\exception\BaseException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function addTopicVisit(int $topic_id = 0): bool
    {
        $insert = $this->createWhere('topic', input('ulog_rid', $topic_id), 1);
        return $this->addVisit($insert);
    }


    /**
     * 通过用户ID获取专题记录
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getTopicVisitByUid()
    {
        $where = $this->createWhere('topic', null, 4);
        return $this->list('topic', $where, [
            'withField' => '*'
        ]);
    }

    /**
     * 添加用户点赞记录
     * @param string $type
     * @param int $id
     * @return bool|void
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function support(string $type, int $id): bool
    {
        $where = $this->createWhere($type, $id, 3);
        $row = $this->where($where)->find();
        $this->countHits($type, $id, 5);
        if ($row) { // 存在 取消赞
            Db::name($type)
                ->where($type . '_id', $id)
                ->dec($type . '_up')
                ->update();
            return $this->where($where)->delete();
        }
        // 点赞成功
        Db::name($type)
            ->where($type . '_id', $id)
            ->inc($type . '_up')
            ->update();
        return $this->create($where) ? true : false;
    }

    /**
     * 通过用户ID获取影片播放记录
     * @return array
     * @throws \app\lib\exception\BaseException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getVodPlayByUid()
    {
        $where = $this->createWhere('vod', null, 4);
        return $this->list('vod', $where, [
            'withField' => 'vod_id,vod_name,vod_pic,vod_remarks,vod_score',
            'sort' => 'update_time desc'
        ]);
    }

    /**
     * 通过用户ID获取影片点赞记录
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getVodSupportByUid()
    {
        $where = $this->createWhere('vod', null, 3);
        return $this->list('vod', $where, [
            'withField' => 'vod_id,vod_name,vod_pic,vod_remarks,vod_score'
        ]);
    }

    /**
     * 用户是否已点赞
     * @param string $type
     * @param int $rid
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function isSupportByRid(string $type, int $rid): bool
    {
        return $this->getFind($type, $rid, 3) ? true : false;
    }

    /**
     * 用户是否收藏
     * @param string $type
     * @param int $rid
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function isCollectByRid(string $type, int $rid): bool
    {
        return $this->getFind($type, $rid, 2) ? true : false;
    }

    /**
     * 添加用户收藏记录
     * @param $type
     * @param $id
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function collect($type, $id): bool
    {
        $where = $this->createWhere($type, $id, 2);
        $row = $this->where($where)->find();
        $this->countHits($type, $id, 10);
        if ($row) return $this->where($where)->delete();
        return $this->create($where) ? true : false;
    }

    /**
     * 通过用户ID获取影片收藏记录
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getVodCollectByUid()
    {
        $where = $this->createWhere('vod', null, 2);
        return $this->list('vod', $where, [
            'withField' => 'vod_id,vod_name,vod_pic,vod_remarks,vod_score'
        ]);
    }

    /**
     * 获取收藏数量
     * @param $type
     * @param $id
     * @return int
     */
    public function getCollectCount(string $type, int $id): int
    {
        $options = ['vod' => 1, 'art' => 2, 'topic' => 3];
        $where = [
            'ulog_rid' => $id,
            'ulog_mid' => $options[$type],
            'ulog_type' => 2
        ];
        return $this->where($where)->count();
    }

    /**
     * 获取用户单条记录
     * @param string $type
     * @param int $rid
     * @param int $action
     * @param int $sid
     * @param int $nid
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getFind(string $type, int $rid, int $action = 0, int $sid = null, int $nid = null): array
    {
        $where = $this->createWhere($type, $rid, $action, $sid, $nid);
        $res = $this->where($where)->find();
        return $res ? $res->toArray() : [];
    }

    /**
     * 获取用户所有记录
     * @param string $type
     * @param int $rid
     * @param int $action
     * @param int $sid
     * @param int $nid
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getAll(string $type, int $rid, int $action = 0, int $sid = null, int $nid = null): array
    {
        $where = $this->createWhere($type, $rid, $action, $sid, $nid);
        $res = $this->where($where)->select();
        return $res ? $res->toArray() : [];
    }

    /**
     * 生成 where条件
     * @param $type
     * @param $id
     * @param $action
     * @param int $sid
     * @param int $nid
     * @return array
     */
    private function createWhere(string $type, int $id = null, int $action = null, int $sid = null, int $nid = null): array
    {
        $options = ['vod' => 1, 'art' => 2, 'topic' => 3];
        $where = ['user_id' => request()->userId, 'ulog_mid' => $options[$type]];
        if ($id != null) $where['ulog_rid'] = $id;
        if ($sid != null) $where['ulog_sid'] = $sid;
        if ($nid != null) $where['ulog_nid'] = $nid;
        if ($action != null) $where['ulog_type'] = $action;
        return $where;
    }
}