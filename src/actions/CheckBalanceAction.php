<?php

namespace glsv\smssender\actions;

use glsv\smssender\exceptions\SmsSenderException;

/**
 * Class CheckBalanceAction
 * @package glsv\smssender\actions
 *
 * Проверить баланс у sms-провайдера
 */
class CheckBalanceAction extends BaseSmsSenderAction
{
    /**
     * Путь редиректа после выполнения
     * @var array
     */
    public $redirectPath = ['index'];

    public function run()
    {
        try {
            $balance = $this->sender->getBalance();
            \Yii::$app->session->addFlash(
                'success',
                \Yii::t('sms-sender/app', 'Balance') . ': ' . $this->sender->getCurrentProviderKey() . ': ' . $balance
            );
        } catch (SmsSenderException $exc) {
            \Yii::$app->errorHandler->logException($exc);
            \Yii::$app->session->addFlash('error', $exc->getMessage());
        }

        $this->controller->redirect($this->redirectPath);
    }
}