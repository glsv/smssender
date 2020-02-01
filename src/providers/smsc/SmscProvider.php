<?php

namespace glsv\smssender\providers\smsc;

use evgenyy33\smsaerov2\SmsaeroApiV2;
use glsv\smssender\exceptions\FailedCallServiceException;
use glsv\smssender\exceptions\ValidateException;
use glsv\smssender\exceptions\ValidateResponseException;
use glsv\smssender\interfaces\SendResponseInterface;
use glsv\smssender\interfaces\SmsLogModelInterface;
use glsv\smssender\interfaces\SmsProviderInterface;
use glsv\smssender\providers\smsc\api\SmscApi;

class SmscProvider implements SmsProviderInterface
{
    CONST KEY = 'smsc';

    private $login;
    private $psw;

    /**
     * @var \glsv\smssender\providers\smsc\api\SmscApi
     */
    private $api;

    public function __construct($config)
    {
        if (empty($config)) {
            throw new \InvalidArgumentException('The config param can`t be empty.');
        }

        if (!is_array($config)) {
            throw new \InvalidArgumentException('The config must be an array.');
        }

        $required_fields = ['login', 'psw'];

        foreach ($required_fields as $param) {
            if (!isset($config[$param])) {
                throw new \InvalidArgumentException($param . ' parameter doesn`t defined in the config.');
            }
        }

        foreach ($config as $field => $value) {
            if (property_exists(static::class, $param)) {
                $this->$field = $value;
            }
        }

        $this->api = new SmscApi($this->login, $this->psw);
        $this->api->setFormat(SmscApi::FORMAT_JSON);
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return self::KEY;
    }

    /**
     * @param string $number
     * @param string $message
     * @param \DateTimeInterface|null $dateSend
     * @return SendResponseInterface
     */
    public function send($number, $message, \DateTimeInterface $dateSend = null)
    {
        if (empty($number)) {
            throw new ValidateException('The number can`t be empty.');
        }

        if (empty($message)) {
            throw new ValidateException('The message can`t be empty.');
        }

        $response = $this->api->send($number, $message);
        $result = $this->handleResponse($response);

        if (!isset($result['id'])) {
            throw new ValidateResponseException('Response must contain a "id" value.');
        }

        return new SmscSendResponse($result);
    }

    /**
     * @param SmsLogModelInterface $model
     * @return SendResponseInterface
     */
    public function getInfoMessage(SmsLogModelInterface $model)
    {
        $response = $this->api->getStatus($model->getMessageId(), $model->getPhone());
        $result = $this->handleResponse($response);

        if (!isset($result['status'])) {
            throw new ValidateResponseException('Response must contain a "status" value.');
        }

        return new SmscStatusResponse($model->getMessageId(), $result);
    }

    /**
     * @return string
     */
    public function getBalance()
    {
        $response = $this->api->getBalance();
        $result = $this->handleResponse($response);

        if (!isset($result['balance'])) {
            throw new ValidateResponseException('Response must contain a "balance" value.');
        }

        return (float)$result['balance'];
    }

    /**
     * @param string $response
     * @return mixed
     */
    protected function handleResponse($response)
    {
        if (empty($response)) {
            throw new ValidateResponseException('Response can`t be empty.');
        }

        if (!is_string($response)) {
            throw new ValidateResponseException('Answer must be a string.');
        }

        try {
            $result = json_decode($response, true);
        } catch (\Exception $exc) {
            throw new ValidateResponseException('Answer must be a valid json.');
        }

        if (isset($result['error'])) {
            $arr = [];
            if ($result['error_code']) {
                $arr[] = 'code: ' . $result['error_code'];
            }

            $arr[] = 'message: ' . $result['error'];

            throw new FailedCallServiceException(implode(', ', $arr));
        }

        return $result;
    }
}