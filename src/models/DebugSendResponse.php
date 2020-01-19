<?php

namespace glsv\smssender\models;

use glsv\smssender\interfaces\SendResponseInterface;

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
    public function getMessageStatus()
    {
        return 'debug';
    }

    /**
     * @return string
     */
    public function getResponse()
    {
        return 'Debug режим';
    }

    /**
     * @return bool
     */
    public function isFakeResponse()
    {
        return true;
    }

    /**
     * @return string|null
     */
    public function getProviderStatus()
    {
        return null;
    }
}