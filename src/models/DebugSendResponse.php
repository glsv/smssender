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
        return 1;
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
    public function getProviderStatus(): ProviderMessageStatusInterface
    {
        return new DebugMessageStatus();
    }
}