<?php

namespace glsv\smssender\interfaces;

use glsv\smssender\models\Recipient;

interface SmsFormInterface
{
    /**
     * @return string
     */
    public function getNumber();

    /**
     * @return string
     */
    public function getMessage();

    /**
     * @return bool
     */
    public function hasRecipient();

    /**
     * @return Recipient
     */
    public function getRecipient();

    /**
     * @return \DateTimeInterface
     */
    public function getDateSend();
}