<?php

namespace glsv\smssender\actions;

use glsv\smssender\forms\SmsSimpleSendForm;
use glsv\smssender\vo\SendMethod;

/**
 * Class SendSimpleFormAction
 * @package glsv\smssender\actions
 *
 * Отправка sms через базовую форму
 */
class SendSimpleFormAction extends BaseSmsSenderAction
{
    public $formViewName = 'form';
    public $smsViewName = 'view';

    public function init()
    {
        parent::init();

        if (empty($this->formViewName)) {
            throw new \InvalidArgumentException('viewName не может быть пустым.');
        }
    }

    public function run()
    {
        $form = new SmsSimpleSendForm();

        if ($form->load(\Yii::$app->request->post()) && $form->validate()) {
            if ($form->hasRecipient()) {
                $recipient = $form->getRecipient();
            } else {
                $recipient = null;
            }

            try {
                $this->sender->send($form->number, $form->message, SendMethod::METHOD_MANUAL, null, $recipient);
                \Yii::$app->session->addFlash('success', 'Сообщение успешно отправлено');

                if (!empty($this->smsViewName) && $smsLog = $this->sender->getLastSmsLog()) {
                    return $this->controller->redirect([$this->smsViewName, 'id' => $smsLog->id]);
                } else {
                    return $this->controller->refresh();
                }
            } catch (\Exception $exc) {
                \Yii::$app->session->addFlash('error', $exc->getMessage());
            }
        }

        return $this->controller->render($this->formViewName, ['model' => $form]);
    }
}