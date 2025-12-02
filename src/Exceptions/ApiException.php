<?php

namespace OpenForAll\SDK\Exceptions;

/**
 * API异常类
 */
class ApiException extends \Exception
{
    /**
     * 构造函数
     *
     * @param string $message 错误消息
     * @param int $code 错误代码
     * @param \Throwable|null $previous 上一个异常
     */
    public function __construct($message = "", $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
