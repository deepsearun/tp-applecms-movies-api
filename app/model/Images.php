<?php

namespace app\model;

use think\exception\ValidateException;
use think\facade\Filesystem;

/**
 * Class Images
 * @package app\model
 * @author Xiejiawei<forceboot@hotmail.com>
 */
class Images extends Base
{
    protected $autoWriteTimestamp = true;

    /**
     * 保存上传的图片
     * @param $field
     * @return string|void
     * @throws \app\lib\exception\BaseException
     */
    public static function add($field)
    {
        $file = request()->file($field);
        if (!$file) ApiException('请选择要上传的图片', 10000);
        try {
            validate(['file' => [
                'fileSize' => 1048576,
                'fileExt' => 'jpg,png,gif,jpeg',
                'fileMime' => 'image/jpeg,image/png,image/gif',
            ]])->check(['file' => $file]);
            $saveName = Filesystem::disk('public')
                ->putFile('avatar', $file);
            if (!$saveName) ApiException('图片上传失败', 50000);
            self::create([
                'user_id' => request()->userId ?? 0,
                'url' => getFileUrl($saveName)
            ]);
            return getFileUrl($saveName);
        } catch (ValidateException $e) {
            ApiException($e->getMessage(), 10000);
        }
    }

    /**
     * 图片是否存在
     * @param $id
     * @param $userid
     * @return array|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function isImageExist($id, $userid)
    {
        return $this->where('user_id', $userid)
            ->field('id')
            ->find($id);
    }

}