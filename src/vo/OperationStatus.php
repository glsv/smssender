<?php

namespace glsv\smssender\vo;

class OperationStatus
{
    CONST STATUS_SUCCESS = 'success';
    CONST STATUS_ERROR = 'error';
    CONST STATUS_DEBUG = 'debug';

    public static $statuses = [
        self::STATUS_SUCCESS => 'Выполнено',
        self::STATUS_ERROR => 'Ошибка',
        self::STATUS_DEBUG => 'Debug',
    ];

    private $status;

    public function __construct($status)
    {
        if (!in_array($status, array_keys(self::$statuses))) {
            throw new \InvalidArgumentException('Ошибка статуса (' . $status . ') ' . self::class);
        }

        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return self::$statuses[$this->status];
    }
}