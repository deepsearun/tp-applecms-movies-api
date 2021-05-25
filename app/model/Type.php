<?php

namespace app\model;

/**
 * Class Type
 * @package app\model
 * @author Xiejiawei<forceboot@hotmail.com>
 */
class Type extends Base
{
    protected $pk = 'type_id';

    protected $globalScope = ['status'];

    /**
     * 定义全局的查询范围
     * @param $query
     */
    public function scopeStatus($query)
    {
        $query->where('type_status', 1);
    }

    /**
     * 子分类关联影片
     * @return \think\model\relation\HasMany
     */
    public function vod()
    {
        return $this->hasMany('Vod', 'type_id', 'type_id');
    }

    /**
     * 父分类关联影片
     * @return \think\model\relation\HasMany
     */
    public function vodParent()
    {
        return $this->hasMany('Vod', 'type_id_1', 'type_id');
    }

    /**
     * 限制返回字段
     * @return string
     */
    public function listField(): string
    {
        return 'type_id,type_name,type_pid,type_extend,type_mid';
    }

    /**
     * 查询分类
     * @param array $where
     * @param array $options
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    private function getClass(array $where = [], array $options = []): array
    {
        $field = isset($options['field']) ? $options['field'] : $this->listField();
        $total = $this->where($where)->count();
        $list = $this->field($field)
            ->where($where)
            ->select()
            ->toArray();
        return $this->showResArr($list, $total);
    }

    /**
     * 查询当前父分类
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \app\lib\exception\BaseException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getParentClass(): array
    {
        $params = input();
        $arr = ['vod' => 1, 'art' => 2];
        if (!array_key_exists($params['type'], $arr)) ApiException('非法参数', 10000);
        $where = ['type_mid' => $arr[$params['type']], 'type_pid' => 0];
        return $this->getClass($where, [
            'field' => 'type_id,type_name,type_pid,type_extend'
        ]);
    }

    /**
     * 获取父分类下的子分类
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getSonClassByParent():array
    {
        $params = input();
        $where = ['type_pid' => $params['parent_id']];
        return $this->getClass($where, [
            'field' => 'type_id,type_name,type_pid'
        ]);
    }

    /**
     * 生成筛选where条件
     * @return array
     */
    public static function isScreenVodParamsCreateWhere(): array
    {
        $params = input();
        $where = [];
        if (isset($params['son_id'])) {
            $where[] = ['type_id', '=', $params['son_id']];
        }
        if (isset($params['class'])) {
            $where[] = ['vod_class', '=', $params['class']];
        }
        if (isset($params['area'])) {
            $where[] = ['vod_area', '=', $params['area']];
        }
        if (isset($params['lang'])) {
            $where[] = ['vod_lang', '=', $params['lang']];
        }
        if (isset($params['year'])) {
            $where[] = ['vod_year', '=', $params['year']];
        }
        if (isset($params['letter'])) {
            $where[] = ['vod_letter', '=', $params['letter']];
        }
        return $where;
    }

    /**
     * 筛选影片
     * @param string $with
     * @param array $where
     * @return array
     * @throws \app\lib\exception\BaseException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function screenVod(string $with = '', array $where = []): array
    {
        $params = input();
        $params['vodField'] = (new Vod())->listField();
        $vodWhere = self::isScreenVodParamsCreateWhere();
        $page = intval(input('page', 1));
        $pageSize = intval(input('pageSize', 12));
        $sort = $this->sortByDesc('vod');
        $list = $this->with([$with => function ($query) use ($params, $page, $pageSize, $sort, $vodWhere) {
            $query->field($params['vodField'])
                ->where($vodWhere)
                ->order($sort)
                ->page($page, $pageSize);
        }])->field($this->listField())->where($where)->find();
        if (!$list) return [];
        if ($with == 'vod') {
            $list['type_extend'] = $this->field('type_id,type_extend')
                ->where('type_id', $list['type_pid'])
                ->find()['type_extend'];
        }
        if ($with == 'vodParent') {
            $list['son'] = $this->field($this->listField())
                ->where('type_pid', $list['type_id'])
                ->select();
        }
        return $list->toArray();
    }

    /**
     * 通过父分类ID筛选影片
     * @return array
     * @throws \app\lib\exception\BaseException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function screenVodByParentId(): array
    {
        $list = $this->screenVod('vodParent', [
            'type_id' => input('parent_id'),
            'type_pid' => 0
        ]);
        return $this->showResArr($list);
    }

    /**
     * 通过子分类ID筛选影片
     * @return array
     * @throws \app\lib\exception\BaseException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function screenVodBySonId(): array
    {
        $list = $this->screenVod('vod', [
            ['type_id', '=', input('son_id')],
            ['type_pid', '<>', 0]
        ]);
        return $this->showResArr($list);
    }

}