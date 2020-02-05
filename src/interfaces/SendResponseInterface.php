<?php

namespace glsv\smssender\interfaces;

interface SendResponseInterface
{
    /**
     * @return int
     */
    public function getMessageId();

    /**
     * @return ProviderMessageStatusInterface
     */
    public function getProviderStatus();

    /**
     * @return array
     */
    public function getResponse();

    /**
     * @return int timestamp
     */
    public function getDateLastChangeStatus();
}