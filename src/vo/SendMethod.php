<?php

namespace glsv\smssender\vo;

class SendMethod
{
    CONST METHOD_MANUAL = 'manual';
    CONST METHOD_CRON = 'cron';

    public static $methods = [
        self::METHOD_MANUAL,
        self::METHOD_CRON,
    ];

    private $method;

    public function __construct($method)
    {
        if (in_array($method, self::$methods)) {
            throw new \InvalidArgumentException('Метод отправки не верен: ' . $method);
        }

        $this->method = $method;
    }

    public function getMethod()
    {
        return $this->method;
    }
}