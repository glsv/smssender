<?php

namespace glsv\smssender\providers\smsAero;

use glsv\smssender\interfaces\ProviderMessageStatusInterface;
use glsv\smssender\vo\MessageStatus;

class SmsAeroMessageStatus implements ProviderMessageStatusInterface
{
    CONST STATUS_QUEUE = 0;
    CONST STATUS_DELIVERED = 1;
    CONST STATUS_NOT_DELIVERED = 2;
    CONST STATUS_WAIT_STATUS = 4;
    CONST STATUS_TRANSFERRED = 3;
    CONST STATUS_REJECTED = 6;
    CONST STATUS_MODERATION = 8;

    public static $statuses = [
        self::STATUS_QUEUE => 'В очереди',
        self::STATUS_DELIVERED => 'Доставлено',
        self::STATUS_NOT_DELIVERED => 'Не доставлено',
        self::STATUS_WAIT_STATUS => 'Ожидание статуса сообщения',
        self::STATUS_TRANSFERRED => 'Передано',
        self::STATUS_REJECTED => 'Сообщение отклонено',
        self::STATUS_MODERATION => 'На модерации',
    ];

    private static $map = [
        self::STATUS_QUEUE => MessageStatus::STATUS_QUEUED,
        self::STATUS_DELIVERED => MessageStatus::STATUS_DELIVERED,
        self::STATUS_NOT_DELIVERED => MessageStatus::STATUS_FAILED,
        self::STATUS_WAIT_STATUS => MessageStatus::STATUS_UNDEFINED,
        self::STATUS_TRANSFERRED => MessageStatus::STATUS_SENDING,
        self::STATUS_REJECTED => MessageStatus::STATUS_REJECTED,
        self::STATUS_MODERATION => MessageStatus::STATUS_MODERATION,
    ];

    /**
     * @var int
     */
    private $code;

    /**
     * Status constructor.
     * @param int $code
     */
    public function __construct($code)
    {
        if (!in_array($code, array_keys(self::$statuses))) {
            throw new \InvalidArgumentException('Code status of message is wrong: ' . $code);
        }

        $this->code = $code;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return self::$statuses[$this->code];
    }

    /**
     * @return string
     */
    public function getSenderStatus()
    {
        if (!isset(self::$map[$this->code])) {
            throw new \DomainException('A provider code status doesn`t defined for code: ' . $this->code);
        }

        return self::$map[$this->code];
    }
}