<?php

namespace glsv\smssender\providers\smsc;

use glsv\smssender\interfaces\MessageStatusInterface;
use glsv\smssender\interfaces\ProviderMessageStatusInterface;
use glsv\smssender\vo\MessageStatus;

class SmscMessageStatus implements MessageStatusInterface, ProviderMessageStatusInterface
{
    /**
     * Расшифровка статусов
     * https://smsc.ru/api/http/status_messages/statuses/
     */
    CONST STATUS_NOT_FOUND = -3;
    CONST STATUS_STOPPED = -2;
    CONST STATUS_QUEUE = -1;
    CONST STATUS_TRANSFERRED = 0;
    CONST STATUS_DELIVERED = 1;
    CONST STATUS_OPENED = 2;
    CONST STATUS_EXPIRED = 3;
    CONST STATUS_TOUCHED_LINK = 4;
    CONST STATUS_IMPOSSIBLE_DELIVERY = 20;
    CONST STATUS_WRONG_PHONE_NUMBER = 22;
    CONST STATUS_DENIED = 23;
    CONST STATUS_NO_MONEY = 24;
    CONST STATUS_NOT_AVAILABLE = 25;

    public static $statuses = [
        self::STATUS_NOT_FOUND => 'Не найдено',
        self::STATUS_STOPPED => 'Остановлено',
        self::STATUS_QUEUE => 'Ожидает отправки',
        self::STATUS_TRANSFERRED => 'Передано оператору',
        self::STATUS_DELIVERED => 'Доставлено',
        self::STATUS_OPENED => 'Прочитано',
        self::STATUS_EXPIRED => 'Просрочено',
        self::STATUS_TOUCHED_LINK => 'Нажата ссылка',
        self::STATUS_IMPOSSIBLE_DELIVERY => 'Невозможно доставить',
        self::STATUS_WRONG_PHONE_NUMBER => 'Неверный номер',
        self::STATUS_DENIED => 'Запрещено',
        self::STATUS_NO_MONEY => 'Недостаточно средств',
        self::STATUS_NOT_AVAILABLE => 'Недоступный номер',
    ];

    private static $map = [
        self::STATUS_NOT_FOUND => MessageStatus::STATUS_FAILED,
        self::STATUS_STOPPED => MessageStatus::STATUS_FAILED,
        self::STATUS_QUEUE => MessageStatus::STATUS_QUEUED,
        self::STATUS_TRANSFERRED => MessageStatus::STATUS_SENDING,
        self::STATUS_DELIVERED => MessageStatus::STATUS_DELIVERED,
        self::STATUS_OPENED => MessageStatus::STATUS_DELIVERED,
        self::STATUS_EXPIRED => MessageStatus::STATUS_FAILED,
        self::STATUS_TOUCHED_LINK => MessageStatus::STATUS_DELIVERED,
        self::STATUS_IMPOSSIBLE_DELIVERY => MessageStatus::STATUS_FAILED,
        self::STATUS_WRONG_PHONE_NUMBER => MessageStatus::STATUS_FAILED,
        self::STATUS_DENIED => MessageStatus::STATUS_REJECTED,
        self::STATUS_NO_MONEY => MessageStatus::STATUS_REJECTED,
        self::STATUS_NOT_AVAILABLE => MessageStatus::STATUS_FAILED,
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