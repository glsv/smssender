<?php

namespace glsv\smssender\interfaces;

interface SmsLogModelInterface
{
    public function getMessageId();

    /**
     * @return string
     */
    public function getPhone();
}