<?php

namespace glsv\smssender\interfaces;

interface SendResponseInterface
{
    /**
     * @return bool
     */
    public function isFakeResponse();

    /**
     * @return int
     */
    public function getMessageId();

    /**
     * @return string
     */
    public function getMessageStatus();

    /**
     * @return string
     */
    public function getProviderStatus();

    /**
     * @return array
     */
    public function getResponse();
}