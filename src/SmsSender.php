<?php

namespace glsv\smssender;

use glsv\smssender\interfaces\SmsFormInterface;
use glsv\smssender\interfaces\SmsSenderInterface;
use glsv\smssender\interfaces\SendResponseInterface;
use glsv\smssender\interfaces\SmsProviderInterface;
use glsv\smssender\services\SmsLogService;
use glsv\smssender\models\DebugSendResponse;
use glsv\smssender\models\ErrorSendResponse;
use glsv\smssender\models\Recipient;
use glsv\smssender\vo\OperationStatus;
use glsv\smssender\vo\SendMethod;
use glsv\smssender\exceptions\ValidateException;
use glsv\smssender\exceptions\FailedCallServiceException;

class SmsSender implements SmsSenderInterface
{
    /**
     * @var SmsProviderInterface
     */
    protected $provider;

    /**
     * @var bool
     */
    protected $fake_mode = false;

    /**
     * @var bool
     */
    protected $log_enabled = true;

    /**
     * @var SmsLogService
     */
    protected $service;

    /**
     * @var SmsLog|null
     */
    protected $last_sms_log;

    public function __construct(SmsProviderInterface $provider, SmsLogService $service)
    {
        $this->service = $service;
        $this->provider = $provider;
    }

    public function fakeModeEnable()
    {
        $this->fake_mode = true;
    }

    public function fakeModeDisable()
    {
        $this->fake_mode = false;
    }

    /**
     * @return SmsProviderInterface
     */
    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * @param string $number
     * @param string $message
     * @param string $method
     * @param \DateTimeInterface|null $dateSend
     * @param Recipient|null $recipient
     * @return SendResponseInterface
     */
    public function send($number, $message, $method, \DateTimeInterface $dateSend = null, Recipient $recipient = null)
    {
        $number = (string)$number;

        if (strlen($number) != 11) {
            throw new ValidateException('Номер телефона должен состоять из 11 цифр.');
        }

        if (empty($message)) {
            throw new ValidateException('Пустое сообщение.');
        }

        if (!in_array($method, SendMethod::$methods)) {
            throw new ValidateException('Ошибочный метод: ' . $method);
        }

        $provider = $this->provider;

        try {
            if ($this->fake_mode) {
                $response = new DebugSendResponse();
                $op_status = OperationStatus::STATUS_DEBUG;
            } else {
                $response = $provider->send($number, $message, $dateSend);
                $op_status = OperationStatus::STATUS_SUCCESS;
            }

            $this->validateSendResponse($response);
            $this->log($op_status, $number, $message, $method, $response, $recipient);

            return $response;
        } catch (FailedCallServiceException $exc) {
            $response = new ErrorSendResponse($exc->getMessage());
            $this->log(OperationStatus::STATUS_ERROR, $number, $message, $method, $response, $recipient);

            throw $exc;
        }
    }

    /**
     * @param $operation_status
     * @param $number
     * @param $message
     * @param $method
     * @param SendResponseInterface|null $response
     * @param Recipient|null $recipient
     */
    protected function log($operation_status, $number, $message, $method, SendResponseInterface $response, Recipient $recipient = null)
    {
        if (!$this->log_enabled) {
            return;
        }

        $provider = $this->provider;

        $log = new SmsLog([
            'phone' => $number,
            'message' => $message,
            'method' => $method,
            'provider_key' => $provider->getKey(),
            'operation_status' => $operation_status,
        ]);

        $log->setDataFromSendResponse($response);

        if ($recipient) {
            $log->setRecipient($recipient);
        }

        $this->service->save($log);
        $this->last_sms_log = $log;
    }

    /**
     * @param int|string $message_id
     * @return SendResponseInterface
     */
    public function getInfoMessage($message_id)
    {
        $response = $this->provider->getInfoMessage($message_id);
        $this->validateSendResponse($response);

        return $response;
    }

    /**
     * @param SendResponseInterface[]|SendResponseInterface $responses
     */
    protected function validateSendResponse($responses)
    {
        if (!is_array($responses)) {
            $responses = [$responses];
        }

        foreach ($responses as $r) {
            if (!$r instanceof SendResponseInterface) {
                throw new \RuntimeException('Ответ от провайдера ' . get_class($this->provider) . ' должен быть в виде объекта SendResponseInterface');
            }
        }
    }

    /**
     * @return string
     */
    public function getBalance()
    {
        return $this->provider->getBalance();
    }

    /**
     * @return string
     */
    public function getCurrentProviderKey()
    {
        return $this->provider->getKey();
    }

    /**
     * @return false|SmsLog
     */
    public function getLastSmsLog()
    {
        if (!empty($this->last_sms_log)) {
            return $this->last_sms_log;
        }

        return false;
    }

    /**
     * @param SmsFormInterface $form
     * @return SendResponseInterface
     */
    public function sendByForm(SmsFormInterface $form)
    {
        $recipient = $form->hasRecipient() ? $form->getRecipient() : null;
        return $this->send($form->getNumber(), $form->getMessage(), SendMethod::METHOD_MANUAL, $form->getDateSend(), $recipient);
    }

    /**
     * @return void
     */
    public function logDisable()
    {
        $this->log_enabled = false;
    }
}
