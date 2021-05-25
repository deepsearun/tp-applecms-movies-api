<?php

namespace app\lib\exception;

use think\exception\Handle;

use think\Response;

use Throwable;

class ExceptionHandler extends Handle
{

    /**
     * @var int 状态码
     */
    public $code;

    /**
     * @var string 错误信息
     */
    public $msg;

    /**
     * @var int 错误状态码
     */
    public $errorCode;

    /**
     * 异常处理接管
     * @param \think\Request $request
     * @param Throwable $e
     * @return Response
     */
    public function render($request, Throwable $e): Response
    {
        if ($e instanceof BaseException) {
            $this->code = $e->code;
            $this->msg = $e->msg;
            $this->errorCode = $e->errorCode;
        } else {
            //调试模式下 输出框架默认错误提示
            if (env('APP_DEBUG')) return parent::render($request,$e);
            $this->code = 500;
            $this->msg = '服务器异常';
            $this->errorCode = 999;
        }
        return json([
            'msg' => $this->msg,
            'errorCode' => $this->errorCode
        ], $this->code);
    }
}