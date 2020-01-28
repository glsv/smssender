<?php

namespace glsv\smssender\providers\smsAero;

use glsv\smssender\exceptions\ValidateResponseException;
use glsv\smssender\interfaces\SendResponseInterface;

/**
 * Class SmsAeroResponse
 * @package glsv\smssender\providers\smsAero
 *
 * Ответ SmsAero
 * {
    "id": 1,
    "from": "SMS Aero",
    "number": "79990000000",
    "text": "your text",
    "status": 1,
    "extendStatus": "delivery",
    "channel": "DIRECT",
    "cost": "1.95",
    "dateCreate": 1510656981,
    "dateSend": 1510656981,
   },
 */
class SmsAeroSendResponse implements SendResponseInterface
{
    private $id;
    private $from;
    private $number;
    private $text;
    private $status;
    private $extendStatus;
    private $channel;
    private $cost;
    private $dateCreate;
    private $dateSend;
    private $raw_response;

    /**
     * SmsAeroResponse constructor.
     * @param $response
     */
    public function __construct($response)
    {
        if (!is_array($response)) {
            throw new \InvalidArgumentException('"response" value must be an array.');
        }

        $required = ['id', 'number', 'status', 'dateSend'];

        foreach ($required as $field) {
            if (!array_key_exists($field, $response)) {
                throw new ValidateResponseException($field . ' value doesn`t found in the "response".');
            }
        }

        foreach ($response as $param => $value) {
            if (property_exists(self::class, $param)) {
                $this->$param = $value;
            }
        }

        $this->raw_response = $response;
    }

    /**
     * @return int
     */
    public function getMessageId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getMessageStatus()
    {
        $status = new SmsAeroMessageStatus($this->status);
        return $status->getSenderStatus();
    }

    /**
     * @return array
     */
    public function getResponse()
    {
        return $this->raw_response;
    }

    /**
     * @return bool
     */
    public function isFakeResponse()
    {
        return false;
    }

    /**
     * @return string
     */
    public function getProviderStatus()
    {
        $status = new SmsAeroMessageStatus($this->status);
        return (string)$status->getStatus();
    }
}