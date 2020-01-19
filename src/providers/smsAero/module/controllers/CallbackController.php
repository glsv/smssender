<?php

namespace glsv\smssender\providers\smsAero\module\controllers;

use glsv\smssender\exceptions\EntityNotFoundException;
use glsv\smssender\interfaces\SmsSenderInterface;
use glsv\smssender\services\SmsLogService;
use glsv\smssender\SmsSender;
use yii\rest\Controller;
use yii\web\NotFoundHttpException;

class CallbackController extends Controller
{
    public $enableCsrfValidation = false;

    /**
     * @var SmsLogService
     */
    private $service;

    /**
     * @var SmsSender
     */
    private $sender;

    public function __construct($id, $module, SmsSenderInterface $sender, SmsLogService $service, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->service = $service;
        $this->sender = $sender;
    }

    public function actionUpdateStatus()
    {
        $post = \Yii::$app->request->post();

        if (!isset($post['id']) || !isset($post['status'])) {
            throw new \InvalidArgumentException('Нет параметров id или status');
        }

        try {
            $this->service->updateMessageStatus($this->sender->getCurrentProviderKey(), $post['id'], $post['status']);
        } catch (EntityNotFoundException $exc) {
            \Yii::$app->errorHandler->logException($exc);
            throw new NotFoundHttpException('Сообщение не найдено по message_id');
        }

        return [];
    }
}