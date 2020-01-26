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
            $response = $this->sender->getInfoMessage($model->message_id);

            $model->initBySendResponse($response);
            $this->service->save($model);

            \Yii::$app->session->addFlash('success', 'Лог сообщения обновлен.');
        } catch (EntityNotFoundException $exc) {
            \Yii::$app->session->addFlash('error', 'Запись не найдена по id: ' . $id);
        } catch (SmsSenderException $exc) {
            \Yii::$app->errorHandler->logException($exc);
            \Yii::$app->session->addFlash('error', $exc->getMessage());
        }

        $this->controller->redirect([$this->viewName, 'id' => $id]);
    }
}