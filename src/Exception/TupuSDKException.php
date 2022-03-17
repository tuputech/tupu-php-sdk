<?php

namespace Tuputech\Exception;

class TupuSDKException extends \Exception
{
    private $errorCode;
 
    /**
     * TuputechSDKException constructor.
     * @param string $code 异常错误码
     * @param string $message 异常信息
     */
    public function __construct($code = "", $message = "")
    {
        parent::__construct($message, 0);
        $this->errorCode = $code;
    }
 
    /**
     * 返回错误码
     * @return string
     */
    public function getErrorCode()
    {
        return $this->errorCode;
    }
 
    /**
     * 格式化输出异常码，异常信息，请求id
     * @return string
     */
    public function __toString()
    {
        return "[".__CLASS__."]"." code:".$this->errorCode.
            " message:".$this->getMessage();
    }
}