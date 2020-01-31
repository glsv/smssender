<?php

namespace glsv\smssender\widgets;

use yii\base\InvalidConfigException;
use yii\grid\GridView;
use yii\helpers\Html;
use glsv\smssender\interfaces\RecipientUrlBuilder;
use glsv\smssender\SmsLog;
use glsv\smssender\vo\OperationStatus;
use glsv\smssender\vo\MessageStatus;

/**
 * Class SmsLogGridView
 * @package glsv\smssender\widgets
 *
 * GridView для отображения списка логов
 *
 * Использование подобно обычному GridView:
 * ```php
 * <?= SmsLogGridView::widget([
 *     'dataProvider' => $dataProvider,
 * ]) ?
 * ```
 */
class SmsLogGridView extends GridView
{
    /**
     * @var bool Show a recipient column or not
     */
    public $recipientVisible = true;

    /**
     * @var string
     */
    public $smsControllerId = 'sms-log';

    /**
     * @var RecipientUrlBuilder The helper for generation an URL to the recipient entity
     */
    public $recipientUrlBuilder;

    public function init()
    {
        if ($this->recipientUrlBuilder && !($this->recipientUrlBuilder instanceof RecipientUrlBuilder)) {
            throw new InvalidConfigException('The "recipientUrlBuilder" property must have a RecipientUrlBuilder type.');
        }

        $this->columns = [
            [
                'attribute' => 'date_create',
                'label' => \Yii::t('sms-sender/app', 'created_at'),
                'options' => ['style' => 'width: 120px'],
                'format' => 'raw',
                'value' => function(SmsLog $model) {
                    return Html::a(date('d.m.Y в H:i', $model->created_at), [$this->smsControllerId. '/view', 'id' => $model->id]);
                }
            ],
            [
                'attribute' => 'phone',
                'label' => \Yii::t('sms-sender/app', 'phone'),
                'options' => [
                    'style' => 'width: 100px',
                ],
            ],
            [
                'attribute' => 'message',
                'label' => \Yii::t('sms-sender/app', 'message'),
                'filter' => false,
            ],
            [
                'attribute' => 'operation_status',
                'label' => \Yii::t('sms-sender/app', 'operation_status'),
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
                'label' => \Yii::t('sms-sender/app', 'message_status'),
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
                'label' => \Yii::t('sms-sender/app', 'recipient'),
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