<?php
// 应用公共文件
use app\lib\exception\BaseException;
use think\facade\Cache;

/**
 * API异常输出
 * @param string $msg 错误信息
 * @param int $errorCode 错误代码
 * @param int $code 状态码
 * @throws BaseException
 */
function ApiException($msg = '接口异常', $errorCode = 999, $code = 400)
{
    throw new BaseException([
        'code' => $code,
        'msg' => $msg,
        'errorCode' => $errorCode
    ]);
}

/**
 * 生成唯一key
 * @param string $param 附加参数
 * @return string
 */
function createUniqueKey(string $param = 'token'): string
{
    $md5 = md5(uniqid(md5(microtime(true)), true));
    return sha1($md5 . md5($param));
}

/**
 * 字符串转义
 * @param $string
 * @param int $force
 * @param bool $strip
 * @return array|string
 */
function daddslashes($string, $force = 0, $strip = FALSE)
{
    !defined('MAGIC_QUOTES_GPC') && define('MAGIC_QUOTES_GPC', get_magic_quotes_gpc());
    if (!MAGIC_QUOTES_GPC || $force) {
        if (is_array($string)) {
            foreach ($string as $key => $val) {
                $string[$key] = daddslashes($val, $force, $strip);
            }
        } else {
            $string = addslashes($strip ? stripslashes($string) : $string);
        }
    }
    return $string;
}

if (!function_exists('redis')) {
    /**
     * 获取redis操作句柄
     * @return object|Redis
     */
    function redis()
    {
        return Cache::store('redis')->handler();
    }
}

/**
 * 删除字符串中所有的空格
 * @param $str
 * @return mixed
 */
function trimAll($str): string
{
    $oldChar = array(" ", "　", "\t", "\n", "\r");
    $newChar = array("", "", "", "", "");
    return str_replace($oldChar, $newChar, $str);
}

/**
 * 多维数组通过键 进行数组排序
 * @param array $array 排序的数组
 * @param $key mixed 用来排序的键名
 * @param string $type 排序类型 大小写不敏感 desc or asc
 * @return array
 */
function multiArraySort(array $array, $key, string $type = 'asc'): array
{
    // 判断排序类型
    $sortType = strtolower($type) == 'asc' ? SORT_ASC : SORT_DESC;

    foreach ($array as $row_array) {
        if (!is_array($row_array)) return [];

        $key_array[] = $row_array[$key];
    }

    if (!array_multisort($key_array, $sortType, $array)) return [];

    return $array;
}

/**
 * 获取当前IP IP2long
 * @return string
 */
function getIp2long()
{
    return sprintf('%u', ip2long(request()->ip()));
}

/**
 * 关键词替换
 * @param $string
 * @param $keyword
 * @return mixed
 */
function keywordReplace($string, $keyword): string
{
    return str_ireplace($keyword, '|' . $keyword . '|', $string);
}


/**
 * 截取字符串
 * @param $text
 * @param $length
 * @return string
 */
function subtext($text, $length): string
{
    if (mb_strlen($text, 'utf8') > $length) {
        return mb_substr($text, 0, $length, 'utf8') . '...';
    } else {
        return $text;
    }
}

/**
 * 如果含有中文进行urlencode转码
 * @param $str
 * @return bool
 */
function isChineseTextAndUrlEnCode($str): string
{
    if (!preg_match('/[\x{4e00}-\x{9fa5}]/u', $str) > 0) return $str;
    return urlencode($str);
}

/**
 * 解析字符串为数组
 * @param string $rule
 * @param string $str
 * @return array
 */
function explodeByRule(string $rule, string $str): array
{
    if (empty($str)) return [];
    return explode($rule, $str);
}

/**
 * 获取文件完整url
 * @param string $url
 * @param string|bool
 * @return string|void
 */
function getFileUrl($url = '', $domain = null)
{
    if (!$url) return;
    $domain = !is_null($domain) ? $domain . '/storage/' : config('app')['app_host'] . '/storage/';
    return $domain . $url;
}

/**
 * 获取URL地址文件后缀
 * @param $url
 * @return string|string[]
 */
function getUrlExt($url)
{
    return pathinfo($url, PATHINFO_EXTENSION);
}