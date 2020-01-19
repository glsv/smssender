<?php

namespace glsv\smssender;

use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use glsv\smssender\interfaces\SendResponseInterface;
use glsv\smssender\models\Recipient;
use glsv\smssender\vo\SendMethod;
use glsv\smssender\vo\OperationStatus;
use glsv\smssender\vo\MessageStatus;

/**
 * Class SmsLog
 * @package glsv\smssender
 *
 * @property string $phone
 * @property string $message
 * @property string $method
 * @property string $operation_status
 * @property string $provider_key
 * @property string $message_id
 * @property string $last_response
 * @property string $message_status
 * @property string $provider_message_status
 * @property string $recipient_id
 * @property string $recipient_name
 * @property string $delivery_date
 * @property int $created_at
 * @property int $updated_at
 */
class SmsLog extends ActiveRecord
{
    public static function tableName()
    {
        return 'sms_log';
    }

    public function rules()
    {
        return [
            [['phone', 'message', 'method', 'operation_status', 'provider_key'], 'required'],
            [['message_id'], 'integer'],
            [['phone'], 'string', 'max' => 11, 'min' => 11],
            [['message', 'recipient_name'], 'string', 'max' => 255],
            [['recipient_id'], 'string', 'max' => 36],
            [['method'], 'in', 'range' => SendMethod::$methods],
            [['operation_status'], 'in', 'range' => array_keys(OperationStatus::$statuses)],
            [['message_status'], 'in', 'range' => array_keys(MessageStatus::$statuses)],
            [['provider_message_status'], 'string', 'max' => 50],
            [['last_response'], 'string', 'max' => 512],
            [['delivery_date'], 'date', 'format' => 'php:Y-m-d H:i:s']
        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
            ]
        ];
    }

    public function beforeValidate()
    {
        if (!parent::beforeValidate()) {
            return false;
        }

        if ($this->last_response) {
            $this->last_response = mb_substr($this->last_response, 0, 512);
        }

        if ($this->recipient_name) {
            $this->recipient_name = mb_substr($this->recipient_name, 0, 255);
        }

        if ($this->message) {
            $this->message = mb_substr($this->message, 0, 255);
        }

        return true;
    }

    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'phone' => 'Номер телефона',
            'message' => 'Сообщение',
            'recipient_id' => 'ID получателя',
            'recipient_name' => 'Имя получателя',
            'method' => 'Метод отправки',
            'provider_key' => 'ID провайдера',
            'message_id' => 'ID сообщения у провайдера',
            'last_response' => 'Последний ответ',
            'operation_status' => 'Статус операции',
            'message_status' => 'Статус сообщения',
            'provider_message_status' => 'Статус сообщения провайдера',
            'delivery_date' => 'Дата доставки',
        );
    }

    /**
     * @return bool
     */
    public function isSuccessOperation()
    {
        return $this->operation_status === OperationStatus::STATUS_SUCCESS;
    }

    /**
     * @return bool
     */
    public function isErrorOperation()
    {
        return $this->operation_status === OperationStatus::STATUS_ERROR;
    }

    /**
     * @return bool
     */
    public function isDebugOperation()
    {
        return $this->operation_status === OperationStatus::STATUS_DEBUG;
    }

    public function isDelivered()
    {
        return $this->message_status === MessageStatus::STATUS_DELIVERED;
    }

    public function isFailed()
    {
        return
            $this->message_status === MessageStatus::STATUS_FAILED ||
            $this->message_status === MessageStatus::STATUS_REJECTED;
    }

    public function __toString()
    {
        return 'SMS на ' . $this->phone . ' от ' . date('d.m.Y', $this->created_at);
    }

    /**
     * Установить статус сообщения (и общего MessageStatus и от провайдера)
     * @param $message_status
     * @param null|string $provider_status
     */
    public function setMessageStatus($message_status, $provider_status = null)
    {
        // Если статус сообщения известен, то значит операция выполнена
        $this->operation_status = OperationStatus::STATUS_SUCCESS;
        $this->message_status = $message_status;

        if ($provider_status) {
            $this->provider_message_status = $provider_status;
        }
    }

    public function delivery($delivery_timestamp = null)
    {
        if (!is_int($delivery_timestamp)) {
            throw new \InvalidArgumentException('delivery_timestamp должен быть в формате timestamp: ' . $delivery_timestamp);
        }

        $this->operation_status = OperationStatus::STATUS_SUCCESS;
        $this->message_status = MessageStatus::STATUS_DELIVERED;

        if (!$delivery_timestamp) {
            $delivery_timestamp = time();
        }

        $this->delivery_date = date('Y-m-d H:i:s', $delivery_timestamp);
    }

    /**
     * @param SendResponseInterface $response
     */
    public function setDataFromSendResponse(SendResponseInterface $response)
    {
        $this->message_status = $response->getMessageStatus();
        $this->provider_message_status = $response->getProviderStatus();

        $this->setMessageId($response->getMessageId());
        $this->setResponse($response->getResponse());
    }

    public function setResponse($response)
    {
        if (!is_string($response)) {
            $response = json_encode($response, JSON_UNESCAPED_UNICODE, 10);
        }

        $this->last_response = $response;
    }

    /**
     * @param Recipient $recipient
     */
    public function setRecipient(Recipient $recipient)
    {
        $this->recipient_id = $recipient->getId();
        $this->recipient_name = $recipient->getName();
    }

    /**
     * @param string|int $message_id
     */
    public function setMessageId($message_id)
    {
        $this->message_id = $message_id;
    }

    /**
     * @return MessageStatus
     */
    public function getMessageStatusModel()
    {
        if ($this->message_status) {
            return new MessageStatus($this->message_status);
        }

        return new MessageStatus(MessageStatus::STATUS_UNDEFINED);
    }

    /**
     * @return OperationStatus
     */
    public function getOperationStatusModel()
    {
        return new OperationStatus($this->operation_status);
    }
}