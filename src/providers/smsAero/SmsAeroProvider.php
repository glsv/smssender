<?php

namespace glsv\smssender\providers\smsAero;

use glsv\smssender\exceptions\ValidateResponseException;
use glsv\smssender\exceptions\FailedCallServiceException;
use glsv\smssender\interfaces\SmsProviderInterface;
use glsv\smssender\interfaces\SendResponseInterface;
use evgenyy33\smsaerov2\SmsaeroApiV2;

class SmsAeroProvider implements SmsProviderInterface
{
    CONST CHANNEL_INFO = 'INFO';
    CONST CHANNEL_DIRECT = 'DIRECT';
    CONST CHANNEL_DIGITAL = 'DIGITAL';
    CONST CHANNEL_INTERNATIONAL = 'INTERNATIONAL';
    CONST CHANNEL_SERVICE = 'SERVICE';

    public $user;
    public $api_key;
    public $sign;
    public $channel = self::CHANNEL_DIRECT;
    public $callbackUrl;
    public $debug_mode = false;

    /**
     * @var SmsaeroApiV2
     */
    private $api;

    /**
     * SmsAeroProvider constructor.
     * @param array $config
     */
    public function __construct($config = [])
    {
        if (empty($config)) {
            throw new \InvalidArgumentException('The config param can`t be empty.');
        }

        if (!is_array($config)) {
            throw new \InvalidArgumentException('The config must be an array.');
        }

        $required_fields = ['user', 'api_key'];

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

        $this->api = new SmsaeroApiV2($this->user, $this->api_key, $this->sign);
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return 'SmsAero';
    }

    /**
     * @param string $number
     * @param string $message
     * @param \DateTimeInterface|null $dateSend
     * @return SendResponseInterface
     */
    public function send($number, $message, \DateTimeInterface $dateSend = null)
    {
        if ($dateSend) {
            $date_send = $dateSend->getTimestamp();
        } else {
            $date_send = null;
        }

        if ($this->debug_mode) {
            $method = 'test_send';
        } else {
            $method = 'send';
        }

        $response = call_user_func([$this->api, $method], $number, $message, $this->channel, $date_send, $this->callbackUrl);

        return $this->getResponseModel($response);
    }

    /**
     * @param array $response
     */
    private function validateResponse($response = [])
    {
        if (empty($response)) {
            throw new ValidateResponseException('Response can`t be empty.');
        }

        if (!is_array($response)) {
            throw new ValidateResponseException('Answer must be an array.');
        }

        $fields = ['success', 'data', 'message'];

        foreach ($fields as $field) {
            if (!array_key_exists($field, $response)) {
                throw new ValidateResponseException($field . ' field was not found in the response.');
            }
        }
    }

    /**
     * @param array $response
     * @return SendResponseInterface
     */
    private function getResponseModel($response)
    {
        $this->validateResponse($response);

        if (!$response['success']) {
            throw new FailedCallServiceException($response['message']);
        }

        return new SmsAeroSendResponse($response['data']);
    }

    /**
     * @param int|string $id
     * @return SendResponseInterface
     */
    public function getInfoMessage($id)
    {
        $answer = $this->api->check_send($id);
        $result = $this->getResponseModel($answer);

        return $result;
    }

    /**
     * @return string
     */
    public function getBalance()
    {
        $response = $this->api->balance();
        $this->validateResponse($response);

        if (!$response['success']) {
            throw new ValidateResponseException(
                \Yii::t('sms-sender/messages', 'Error checking a balance.') . $response['message']
            );
        }

        if (!isset($response['data']['balance'])) {
            throw new ValidateResponseException(
                \Yii::t('sms-sender/messages', 'Server response does not correspond to API.')
            );
        }

        return $response['data']['balance'];
    }
}
