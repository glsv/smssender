<?php

namespace glsv\smssender\interfaces;

use glsv\smssender\SmsLog;
use glsv\smssender\models\Recipient;
use \DateTimeInterface;

interface SmsSenderInterface
{
    /**
     * @param string $number
     * @param string $message
     * @param string $method
     * @param DateTimeInterface|null $dateSend
     * @param Recipient|null $recipient
     * @return SendResponseInterface
     */
    public function send($number, $message, $method, DateTimeInterface $dateSend = null, Recipient $recipient = null);

    /**
     * @param $message_id
     * @return SendResponseInterface
     */
    public function getInfoMessage($message_id);

    /**
     * @return string
     */
    public function getBalance();

    /**
     * @return string
     */
    public function getCurrentProviderKey();

    /**
     * @return false|SmsLog
     */
    public function getLastSmsLog();

    /**
     * @param SmsFormInterface $form
     * @return SendResponseInterface
     */
    public function sendByForm(SmsFormInterface $form);

    /**
     * @return void
     */
    public function fakeModeEnable();

    /**
     * @return void
     */
    public function fakeModeDisable();

    /**
     * @return void
     */
    public function logDisable();
}