<?php

namespace glsv\smssender\vo;

use Yii;

class OperationStatus
{
    CONST STATUS_SUCCESS = 'success';
    CONST STATUS_ERROR = 'error';
    CONST STATUS_DEBUG = 'debug';

    public static $statuses = [
        self::STATUS_SUCCESS => 'performed',
        self::STATUS_ERROR => 'error',
        self::STATUS_DEBUG => 'debug',
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
        return Yii::t('sms-sender/statuses', self::$statuses[$this->status]);
    }
}