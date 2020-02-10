<?php

namespace glsv\smssender\models;

use glsv\smssender\interfaces\ProviderMessageStatusInterface;
use glsv\smssender\interfaces\SendResponseInterface;
use glsv\smssender\vo\MessageStatus;

class DebugSendResponse implements SendResponseInterface
{
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
    public function getResponse()
    {
        return 'debug';
    }

    /**
     * @return ProviderMessageStatusInterface
     */
    public function getProviderStatus()
    {
        return new DebugMessageStatus();
    }

    /**
     * @return int timestamp
     */
    public function getDateLastChangeStatus()
    {
        return time();
    }
}