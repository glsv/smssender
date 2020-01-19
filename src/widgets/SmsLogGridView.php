<?php

namespace glsv\smssender\widgets;

use glsv\smssender\interfaces\RecipientUrlBuilder;
use yii\grid\GridView;
use yii\helpers\Html;
use glsv\smssender\SmsLog;
use glsv\smssender\vo\OperationStatus;
use glsv\smssender\vo\MessageStatus;

class SmsLogGridView extends GridView
{
    public $recipientVisible = true;
    public $smsControllerId = 'sms-log';

    /**
     * Helper для генерации URL к сущности получателя
     * @var RecipientUrlBuilder
     */
    public $recipientUrlBuilder;

    public function init()
    {
        if ($this->recipientUrlBuilder && !($this->recipientUrlBuilder instanceof RecipientUrlBuilder)) {
            throw new \InvalidArgumentException('Не верный тип recipientUrlBuilder.');
        }

        $this->columns = [
            [
                'attribute' => 'date_create',
                'label' => 'Дата',
                'options' => ['style' => 'width: 120px'],
                'format' => 'raw',
                'value' => function(SmsLog $model) {
                    return Html::a(date('d.m.Y в H:i', $model->created_at), [$this->smsControllerId. '/view', 'id' => $model->id]);
                }
            ],
            [
                'attribute' => 'phone',
                'label' => 'Номер',
                'options' => [
                    'style' => 'width: 100px',
                ],
            ],
            [
                'attribute' => 'message',
                'filter' => false,
            ],
            [
                'attribute' => 'operation_status',
                'filter' => OperationStatus::$statuses,
                'contentOptions' => function(SmsLog $model) {
                    $class = '';

                    if ($model->isSuccessOperation() || $model->isDebugOperation()) {
                        $class = 'bg-success';
                    } else if ($model->isErrorOperation()) {
                        $class = 'bg-danger';
                    }

                    return ['class' => $class];
                },
                'value' => function(SmsLog $model) {
                    return $model->getOperationStatusModel()->getLabel();
                }
            ],
            [
                'attribute' => 'message_status',
                'filter' => MessageStatus::$statuses,
                'contentOptions' => function(SmsLog $model) {
                    $class = '';

                    if ($model->isDelivered()) {
                        $class = 'bg-success';
                    } else if ($model->isFailed()) {
                        $class = 'bg-danger';
                    }

                    return ['class' => $class];
                },
                'value' => function(SmsLog $model) {
                    return $model->getMessageStatusModel()->getLabel();
                }
            ],
            [
                'attribute' => 'recipient_name',
                'label' => 'ФИО',
                'format' => 'raw',
                'value' => function ($model) {
                    if ($model->recipient_id && $this->recipientUrlBuilder) {
                        return Html::a($model->recipient_name, $this->recipientUrlBuilder->getUrl($model->recipient_id));
                    }
                    else {
                        return $model->recipient_name;
                    }
                },
                'visible' => $this->recipientVisible,
            ],
        ];

        parent::init();
    }
}