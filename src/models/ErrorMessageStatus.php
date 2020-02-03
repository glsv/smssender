<?php

namespace glsv\smssender\models;

use glsv\smssender\interfaces\ProviderMessageStatusInterface;
use glsv\smssender\vo\MessageStatus;

class ErrorMessageStatus implements ProviderMessageStatusInterface
{
    /**
     * @return int
     */
    public function getStatus()
    {
        return 'error';
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return 'error';
    }

    /**
     * @return string
     */
    public function getSenderStatus()
    {
        return MessageStatus::STATUS_FAILED;
    }
}