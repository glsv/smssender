<?php

namespace glsv\smssender\forms;

use glsv\smssender\vo\MessageStatus;
use glsv\smssender\vo\OperationStatus;

/**
 * Class SmsLogSearch
 * @package glsv\smssender\forms
 *
 * @property string $phone
 * @property string $recipient_name
 * @property string $operation_status
 * @property string $message_status
 * @property int $recipient_id
 */
class SmsLogSearch extends \yii\base\Model
{
    public $phone;
    public $recipient_name;
    public $message_status;
    public $operation_status;
    public $recipient_id;

    public function rules()
    {
        return [
            [['phone', 'recipient_name'], 'string'],
            [['recipient_id'], 'integer'],
            [['operation_status'], 'in', 'range' => array_keys(OperationStatus::$statuses)],
            [['message_status'], 'in', 'range' => array_keys(MessageStatus::$statuses)],
        ];
    }
}