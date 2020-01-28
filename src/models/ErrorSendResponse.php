<?php

namespace glsv\smssender\models;

use glsv\smssender\interfaces\SendResponseInterface;

class ErrorSendResponse implements SendResponseInterface
{
    private $message;

    public function __construct($message)
    {
        if (!is_string($message)) {
            throw new \InvalidArgumentException('The message must be a string type.');
        }

        if (empty($message)) {
            throw new \InvalidArgumentException('The message can`t be empty.');
        }

        $this->message = $message;
    }

    /**
     * @return bool
     */
    public function isFakeResponse()
    {
        return false;
    }

    /**
     * @return int
     */
    public function getMessageId()
    {
        return null;
    }

    /**
     * @return string
     */
    public function getMessageStatus()
    {
        return null;
    }

    /**
     * @return string
     */
    public function getResponse()
    {
        return $this->message;
    }

    /**
     * @return string|null
     */
    public function getProviderStatus()
    {
        return null;
    }
}