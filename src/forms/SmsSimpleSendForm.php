<?php

namespace glsv\smssender\forms;

use glsv\smssender\interfaces\SmsFormInterface;
use glsv\smssender\models\Recipient;
use yii\base\Model;
use Yii;

class SmsSimpleSendForm extends Model implements SmsFormInterface
{
    public $number;
    public $message;
    public $recipient_id;
    public $recipient_name;

    public function rules()
    {
        return [
            [['number', 'message'], 'required'],
            [['number'], 'string', 'min' => 11, 'max' => 11],
            [['recipient_id'], 'string', 'max' => 36],
            [['recipient_name'], 'string', 'max' => 255],
            [['recipient_name'], 'required', 'when' => function() {
                return !empty($this->recipient_id);
            }]
        ];
    }

    public function beforeValidate()
    {
        if (is_integer($this->recipient_id)) {
            $this->recipient_id = (string)$this->recipient_id;
        }

        return parent::beforeValidate();
    }

    public function attributeLabels()
    {
        return [
            'number' => Yii::t('sms-sender/app', 'phone'),
            'message' => Yii::t('sms-sender/app', 'message'),
            'recipient_id' => Yii::t('sms-sender/app', 'recipient_id'),
            'recipient_name' => Yii::t('sms-sender/app', 'recipient'),
        ];
    }

    public function attributeHints()
    {
        return [
            'number' => Yii::t('sms-sender/app', '11 digits'),
            'recipient_id' => Yii::t('sms-sender/app', 'ID in the accounting system'),
        ];
    }

    /**
     * @return bool
     */
    public function hasRecipient()
    {
        return !empty($this->recipient_name);
    }

    /**
     * @return Recipient
     */
    public function getRecipient()
    {
        return new Recipient($this->recipient_name, $this->recipient_id);
    }

    /**
     * @return string
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getDateSend()
    {
        return null;
    }
}