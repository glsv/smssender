<?php

namespace glsv\smssender\vo;

use glsv\smssender\interfaces\MessageStatusInterface;

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
        self::STATUS_QUEUED => 'В очереди',
        self::STATUS_SENDING => 'Доставляется',
        self::STATUS_DELIVERED => 'Доставлено',
        self::STATUS_FAILED => 'Ошибка',
        self::STATUS_REJECTED => 'Отклонено',
        self::STATUS_MODERATION => 'На модерации',
        self::STATUS_UNDEFINED => 'Не определен',
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