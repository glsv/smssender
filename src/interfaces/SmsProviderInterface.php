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
     * @param $id
     * @return SendResponseInterface
     */
    public function getInfoMessage($id);

    /**
     * @return string
     */
    public function getBalance();
}