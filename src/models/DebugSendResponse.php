<?php

namespace glsv\smssender\models;

use glsv\smssender\interfaces\SendResponseInterface;
use glsv\smssender\vo\MessageStatus;

class DebugSendResponse implements SendResponseInterface
{
    /**
     * @return int
     */
    public function getMessageId()
    {
        return 1;
    }

    /**
     * @return string
     */
    public function getMessageStatus()
    {
        return 'debug';
    }

    /**
     * @return string
     */
    public function getResponse()
    {
        return 'Debug mode';
    }

    /**
     * @return string|null
     */
    public function getProviderStatus()
    {
        return MessageStatus::STATUS_DEBUG;
    }

    /**
     * @return string
     */
    public function getProviderStatusLabel()
    {
        return 'debug';
    }
}