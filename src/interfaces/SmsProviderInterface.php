<?php

namespace glsv\smssender\interfaces;

interface SmsProviderInterface
{
    /**
     * @return string
     */
    public function getKey();

    /**
     * @param string $number
     * @param string $message
     * @param \DateTimeInterface|null $dateSend
     * @return SendResponseInterface
     */
    public function send($number, $message, \DateTimeInterface $dateSend = null);

    /**
     * @param SmsLogModelInterface $model
     * @return SendResponseInterface
     */
    public function getInfoMessage(SmsLogModelInterface $model);

    /**
     * @return string
     */
    public function getBalance();
}