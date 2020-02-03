<?php

namespace glsv\smssender\models;

use glsv\smssender\interfaces\ProviderMessageStatusInterface;
use glsv\smssender\vo\MessageStatus;

class DebugMessageStatus implements ProviderMessageStatusInterface
{
    /**
     * @return int
     */
    public function getStatus()
    {
        return 'debug';
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return 'debug';
    }

    /**
     * @return string
     */
    public function getSenderStatus()
    {
        return MessageStatus::STATUS_DEBUG;
    }
}