<?php

namespace Uyson\DcatAdmin\AliyunVod\Exceptions;

class BaseException extends \Exception
{
    public function __construct($message, $code)
    {
        $this->message = $message;
        $this->code = $code;
    }
}
