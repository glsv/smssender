<?php

namespace glsv\smssender\actions;

use glsv\smssender\exceptions\EntityNotFoundException;
use glsv\smssender\exceptions\SmsSenderException;
use glsv\smssender\services\SmsLogService;

/**
 * Class UpdateStatusAction
 * @package glsv\smssender\actions
 *
 * Обновить статус сообщения и вернуться на страницу smsLog
 */
class UpdateStatusAction extends BaseSmsSenderAction
{
    /**
     * @var string
     */
    public $viewName = 'view';

    /**
     * @var SmsLogService
     */
    private $service;

    public function init()
    {
        parent::init();
        $this->service = \Yii::createObject(SmsLogService::class);
    }

    public function run($id)
    {
        try {
            $model = $this->service->get($id);
            $response = $this->sender->getInfoMessage($model);

            $model->initBySendResponse($response);
            $this->service->save($model);

            \Yii::$app->session->addFlash(
                'success',
                \Yii::t('sms-sender/messages', 'The log of sms message was updated.')
            );
        } catch (EntityNotFoundException $exc) {
            \Yii::$app->session->addFlash(
                'error',
                \Yii::t('sms-sender/messages', 'The log item was not found by ID: {id}.', ['id' => $id])
            );
        } catch (SmsSenderException $exc) {
            \Yii::$app->errorHandler->logException($exc);
            \Yii::$app->session->addFlash('error', $exc->getMessage());
        }

        $this->controller->redirect([$this->viewName, 'id' => $id]);
    }
}