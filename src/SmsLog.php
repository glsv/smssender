<?php

namespace glsv\smssender;

use glsv\smssender\interfaces\ProviderMessageStatusInterface;
use glsv\smssender\interfaces\SmsLogModelInterface;
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
 * @property string $provider_status_description
 * @property string $recipient_id
 * @property string $recipient_name
 * @property int $delivered_at
 * @property int $created_at
 * @property int $updated_at
 */
class SmsLog extends ActiveRecord implements SmsLogModelInterface
{
    private $max_length_message = 255;
    private $max_length_recipient = 255;
    private $max_length_response = 1024;

    public static function tableName()
    {
        return '{{%sms_log}}';
    }

    public function rules()
    {
        return [
            [['phone', 'message', 'method', 'operation_status', 'provider_key'], 'required'],
            [['message_id'], 'integer'],
            [['phone'], 'string', 'max' => 11, 'min' => 11],
            [['message'], 'string', 'max' => $this->max_length_message],
            [['recipient_name'], 'string', 'max' => $this->max_length_recipient],
            [['recipient_id'], 'string', 'max' => 36],
            [['method'], 'in', 'range' => SendMethod::$methods],
            [['operation_status'], 'in', 'range' => array_keys(OperationStatus::$statuses)],
            [['message_status'], 'in', 'range' => array_keys(MessageStatus::$statuses)],
            [['provider_message_status'], 'string', 'max' => 50],
            [['provider_status_description'], 'string', 'max' => 255],
            [['last_response'], 'string', 'max' => $this->max_length_response],
            [['delivered_at'], 'integer']
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
            $this->last_response = mb_substr($this->last_response, 0, $this->max_length_response);
        }

        if ($this->recipient_name) {
            $this->recipient_name = mb_substr($this->recipient_name, 0, $this->max_length_recipient);
        }

        if ($this->message) {
            $this->message = mb_substr($this->message, 0, $this->max_length_message);
        }

        return true;
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

    /**
     * @return bool
     */
    public function isDelivered()
    {
        return $this->message_status === MessageStatus::STATUS_DELIVERED;
    }

    /**
     * @return bool
     */
    public function isFailed()
    {
        return
            $this->message_status === MessageStatus::STATUS_FAILED ||
            $this->message_status === MessageStatus::STATUS_REJECTED;
    }

    public function __toString()
    {
        return 'SMS to ' . $this->phone . ' (' . date('d.m.Y', $this->created_at) .')';
    }

    public function successOperation()
    {
        $this->operation_status = OperationStatus::STATUS_SUCCESS;
    }

    /**
     * Установить статус сообщения
     * @param ProviderMessageStatusInterface $provider_status
     */
    public function setMessageStatus(ProviderMessageStatusInterface $providerStatus)
    {
        $this->message_status = $provider_status->getSenderStatus();
        $this->provider_message_status = $provider_status->getStatus();
        $this->provider_status_description = $provider_status->getLabel();

        $statusModel = new MessageStatus($this->message_status);

        if ($statusModel->isDelivered()) {
            $this->delivered_at = time();
        }
    }

    /**
     * Иницилизировать свойства по ответу от sms-провайдера
     * @param SendResponseInterface $response
     */
    public function initBySendResponse(SendResponseInterface $response)
    {
        $this->setMessageId($response->getMessageId());
        $this->setResponse($response->getResponse());
        $this->setMessageStatus($response->getProviderStatus());
    }

    /**
     * @param string $response
     */
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

    public function getMessageId()
    {
        return $this->message_id;
    }

    /**
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }
}
