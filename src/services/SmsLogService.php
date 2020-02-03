<?php

namespace glsv\smssender\services;

use glsv\smssender\exceptions\EntityNotFoundException;
use glsv\smssender\forms\SmsLogSearch;
use glsv\smssender\providers\smsAero\SmsAeroMessageStatus;
use glsv\smssender\SmsLog;
use yii\data\ActiveDataProvider;
use RuntimeException;

class SmsLogService
{
    /**
     * @param int $id
     * @return SmsLog
     */
    public function get($id)
    {
        if (!$model = SmsLog::findOne($id)) {
            throw new EntityNotFoundException('SmsLog model was not found by id: ' . $id);
        }

        return $model;
    }

    /**
     * @param string $provider_key
     * @param int $message_id
     * @return SmsLog
     */
    public function getByMessageId($provider_key, $message_id)
    {
        /**
         * @var SmsLog $model
         */
        $model = SmsLog::find()->andWhere(['provider_key' => $provider_key, 'message_id' => $message_id])->one();

        if (!$model) {
            throw new EntityNotFoundException('SmsLog model was not found by message_id: ' . $message_id);
        }

        return $model;
    }

    /**
     * @param string $provider_key
     * @param string|int $message_id
     * @param ProviderMessageStatusInterface $provider_status
     */
    public function updateMessageStatus($provider_key, $message_id, ProviderMessageStatusInterface $provider_status)
    {
        $model = $this->getByMessageId($provider_key, $message_id);
        $model->successOperation();
        $model->setMessageStatus($providerStatus);

        $this->save($model);
    }

    /**
     * @param SmsLogSearch $form
     * @return false|\yii\data\ActiveDataProvider
     */
    public function search(SmsLogSearch $form)
    {
        $query = SmsLog::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'attributes' => ['number', 'message_status', 'operation_status', 'created_at', 'id'],
                'defaultOrder' => ['created_at' => SORT_DESC],
            ],
        ]);

        if (!$form->validate()) {
            $query->andWhere(['id' => -1]);
            return $dataProvider;
        }

        $query->andFilterWhere(
            [
                'message_status' => $form->message_status,
                'operation_status' => $form->operation_status,
                'recipient_id' => $form->recipient_id
            ]
        );

        if (!empty($form->phone)) {
            $query->andWhere(['like', 'phone', $form->phone]);
        }

        if (!empty($form->recipient_name)) {
            $query->andWhere(['like', 'recipient_name', $form->recipient_name]);
        }

        return $dataProvider;
    }

    /**
     * @param SmsLog $model
     */
    public function save(SmsLog &$model)
    {
        if (!$model->save()) {
            throw new RuntimeException('An error occurred while saving the SmsLog: ' . implode(', ', $model->getFirstErrors()));
        }
    }
}