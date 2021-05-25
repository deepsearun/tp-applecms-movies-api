<?php

namespace app\model;

use app\lib\sms\Sms;
use app\lib\exception\BaseException;
use think\facade\Cache;

class User extends Base
{
    protected $pk = 'user_id';

    protected $autoWriteTimestamp = true;

    protected $createTime = 'user_reg_time';

    protected $globalScope = ['group'];

    protected $hidden = ['user_pwd', 'user_random'];

    /**
     * 关联用户组
     * @return \think\model\relation\HasOne
     */
    public function group()
    {
        return $this->hasOne('Group', 'group_id', 'group_id');
    }

    /**
     * 关联设备
     * @return \think\model\relation\HasOne
     */
    public function device()
    {
        return $this->hasOne('Device', 'user_id', 'user_id');
    }

    /**
     * 定义全局的查询范围
     * @param $query
     */
    public function scopeGroup($query)
    {
        $query->with(['group' => function ($query) {
            $query->field('group_id,group_name');
        }]);
    }

    /**
     * 发送验证码
     * @return mixed
     * @throws BaseException
     */
    public function sendCode()
    {
        $phone = input('phone');
        $ip = request()->ip();
        //判断是否满足发送条件
        if (!$this->checkSendCode($phone, $ip)) ApiException('操作频繁', 30001, 200);
        $code = random_int(1000, 9999);
        //没有开启验证码功能
        if (!Sms::checkOpen()) {
            cache('sendCode_' . $phone, $code, Sms::getConfig('expire'));
            cache('sendCode_' . $ip) ? Cache::inc('sendCode_' . $ip) : cache('sendCode_' . $ip, 1, 86400);
            ApiException($code, 30002, 200);
        }
        //发送验证码
        $res = Sms::sendSms($phone, $code);
        //发送成功 写入缓存
        if ($res['Code'] == 'OK') {
            cache('sendCode_' . $ip) ? Cache::inc('sendCode_' . $ip) : cache('sendCode_' . $ip, 1, 86400);
            return cache('sendCode_' . $phone, $code, Sms::getConfig('expire'));
        }
        //发送失败
        ApiException($res['Message'], 30003, 200);
    }

    /**
     * 验证是否满足发送验证码条件
     * @param string $ip 请求ip
     * @param int $phone 手机号
     * @return bool
     */
    public function checkSendCode($phone, $ip): bool
    {
        //缓存是否失效
        if (cache('sendCode_' . $phone)) return false;
        //日ip限制条数
        if (cache('sendCode_' . $ip) >= Sms::getConfig('ipLimit')) return false;

        return true;
    }

    /**
     * 判断用户是否存在
     * @param array $arr
     * @return array|bool|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function isUserExists(array $arr = [])
    {
        if (array_key_exists('user_id', $arr)) { //用户ID
            return $this->where('user_id', $arr['user_id'])->find();
        }
        if (array_key_exists('phone', $arr)) { // 手机号
            $user = $this->where('user_phone', $arr['phone'])->find();
            return $user;
        }
        if (array_key_exists('email', $arr)) { //邮箱
            $user = $this->where('user_email', $arr['email'])->find();
            return $user;
        }
        if (array_key_exists('username', $arr)) { //用户名
            $user = $this->where('user_name', $arr['username'])->find();
            return $user;
        }
        //第三方登录
        if (array_key_exists('provider', $arr)) {
            $where = ['user_openid_' . $arr['provider'] => $arr['openid']];
            $user = $this->where($where)->find();
            return $user;
        }
        return false;
    }

    /**
     * 用户名密码登录
     * @return string
     * @throws BaseException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function login(): string
    {
        $params = input();
        // 验证用户是否存在
        $user = $this->isUserExists($this->filterUserData($params['username']));
        //用户不存在
        if (!$user) ApiException('用户名不存在或未绑定', 20000, 200);
        // 是否被禁用
        $this->checkStatus($user->toArray());
        //验证密码
        $this->checkPassword($params['password'], $user->user_pwd);
        // 更新用户信息
        $this->updateLoginInfo($user->toArray());
        //登录成功
        return $this->createSaveToken($user->toArray());
    }

    /**
     * 手机号登录
     * @return string
     * @throws BaseException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function phoneLogin(): string
    {
        $params = input();
        //验证用户是否存在
        $user = $this->isUserExists(['phone' => $params['phone']]);
        //用户不存在，直接注册
        if (!$user) {
            $user = self::create([
                'group_id' => 2,
                'user_name' => 'user_' . $params['phone'],
                'user_phone' => $params['phone'],
                'user_status' => 1,
                'user_reg_time' => time(),
                'user_reg_ip' => getIp2long(),
                'user_random' => md5(time()),
            ]);
            $this->updateLoginInfo($user->toArray());
            return $this->createSaveToken($user->toArray());
        }
        //是否被禁用
        $this->checkStatus($user->toArray());
        // 更新用户信息
        $this->updateLoginInfo($user->toArray());
        //登录成功
        return $this->createSaveToken($user->toArray());
    }

    /**
     * 更新用户登录信息
     * @param $user
     * @return bool
     * @throws BaseException
     */
    public function updateLoginInfo(array $user): bool
    {
        $update = [
            'user_login_ip' => getIp2long(),
            'user_login_time' => time()
        ];
        if ($user['group_id'] > 2 && $user['user_end_time'] < time()) $update['group_id'] = 2;
        $res = $this->where('user_id', $user['user_id'])->update($update);
        if (!$res) ApiException('登录信息更新失败', 20004, 200);
        return true;
    }

    /**
     * 获取用户信息
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getUserInfo(): array
    {
        $userId = request()->userId;
        return $this->find($userId)->toArray();
    }

    /**
     * 验证密码是否正确
     * @param $input
     * @param $password
     * @return bool
     * @throws BaseException
     */
    public function checkPassword($input, $password)
    {
        if (md5($input) != $password) ApiException('登录密码错误', 20002, 200);
        return true;
    }

    /**
     * 验证用户名格式 手机号 or 昵称 or邮箱
     * @param string $data
     * @return array
     */
    public function filterUserData($data): array
    {
        $arr = [];
        //判断是否是手机号码
        if (preg_match('/^1[3-9][0-9]\d{8}$/', $data)) {
            $arr['phone'] = $data;
            return $arr;
        }
        //判断是否是邮箱
        if (preg_match('#[a-z0-9&\-_.]+@[\w\-_]+([\w\-.]+)?\.[\w\-]+#is', $data)) {
            $arr['email'] = $data;
            return $arr;
        }
        $arr['username'] = $data;
        return $arr;
    }

    /**
     * 修改头像
     * @return string|void
     * @throws BaseException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function changeAvatar()
    {
        $url = Images::add('avatar');
        $user = $this->find(request()->userId);
        $user->user_portrait = $url;
        $user->save();
        return $url;
    }

    /**
     * 修改资料
     * @return bool
     * @throws BaseException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function changeInfo()
    {
        $user = $this->find(request()->userId);
        $user->user_nick_name = input('nickname');
        $user->user_qq = input('qq');
        $user->user_email = input('email');
        $res = $user->save();
        if (!$res) ApiException('修改失败', 50001);
        return $res;
    }

    /**
     * 判断用户是否被禁用
     * @param array $arr
     * @return array
     * @throws BaseException
     */
    public function checkStatus(array $arr = []): array
    {
        if ($arr['user_status'] == 0) ApiException('该账户已被禁用', 20001, 200);
        return $arr;
    }

    /**
     * 创建并保存Token
     * @param array $arr
     * @return string
     * @throws BaseException
     */
    public function createSaveToken(array $arr = []): string
    {
        // 生成token
        $token = createUniqueKey('token');
        $arr['token'] = $token;
        // 登录过期时间
        $expire = array_key_exists('expires_in', $arr) ? $arr['expires_in'] : 0;
        // 保存到缓存中
        if (!cache($token, $arr, $expire)) ApiException();
        // 返回token
        return $token;
    }

    /**
     * 注销登录
     * @return bool
     * @throws BaseException
     */
    public function logout(): bool
    {
        if (!Cache::pull(request()->userToken)) ApiException('您已经退出了', 30004, 200);
        return true;
    }
}