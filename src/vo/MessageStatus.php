<?php

namespace glsv\smssender\vo;

use glsv\smssender\interfaces\MessageStatusInterface;
use Yii;

class MessageStatus implements MessageStatusInterface
{
    CONST STATUS_QUEUED = 'queue';
    CONST STATUS_SENDING = 'sending';
    CONST STATUS_DELIVERED = 'delivered';
    CONST STATUS_FAILED = 'failed';
    CONST STATUS_REJECTED = 'rejected';
    CONST STATUS_MODERATION = 'moderation';
    CONST STATUS_UNDEFINED = 'undefined';
    CONST STATUS_DEBUG = 'debug';

    public static $statuses = [
        self::STATUS_QUEUED => 'queued',
        self::STATUS_SENDING => 'sending',
        self::STATUS_DELIVERED => 'delivered',
        self::STATUS_FAILED => 'failed',
        self::STATUS_REJECTED => 'rejected',
        self::STATUS_MODERATION => 'moderation',
        self::STATUS_UNDEFINED => 'undefined',
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