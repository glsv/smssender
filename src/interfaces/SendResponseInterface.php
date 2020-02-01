<?php

namespace glsv\smssender\interfaces;

interface SendResponseInterface
{
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
     * @return string
     */
    public function getProviderStatusLabel();

    /**
     * @return array
     */
    public function getResponse();
}