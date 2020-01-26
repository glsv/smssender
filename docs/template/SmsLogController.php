<?php
use glsv\smssender\actions\CheckBalanceAction;
use glsv\smssender\actions\SendSimpleFormAction;
use glsv\smssender\actions\UpdateStatusAction;
use glsv\smssender\forms\SmsLogSearch;
use glsv\smssender\interfaces\SmsSenderInterface;
use glsv\smssender\services\SmsLogService;

class SmsLogController extends BackendController
{
    /**
     * @var SmsLogService
     */
    private $service;

    /**
     * @var SmsSenderInterface
     */
    private $sender;

    public function __construct($id, $module, SmsSenderInterface $smsSender, SmsLogService $service, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->service = $service;
        $this->sender = $smsSender;
    }

    public function actions()
    {
        return [
            // Обновить статус sms-сообщения и сделать редирект на view sms
            'update-status' => [
                'class' => UpdateStatusAction::class,
                // Переопределить view на который будет осуществлен редирект после обновления статуса
                // 'viewName' => 'view'
            ],
            // Проверка баланса sms-провайдера и вывод его в session Flash
            'check-balance' => [
                'class' => CheckBalanceAction::class,
                // Для переопределения пути редиректа после проверки баланса
                // 'redirectPath' => ['index']
            ],
            // Простая форма отправки sms
            'form' => [
                'class' => SendSimpleFormAction::class,
                // Переопределить view для формы отправки и просмотра модели лога
                // 'formViewName' => 'form',
                // 'smsViewName' => 'view',
            ]
        ];
    }

    /**
     * Список sms-логов через DataProvider и GridView
     * @return string
     */
    public function actionIndex()
    {
        $form = new SmsLogSearch();

        $form->load(\Yii::$app->request->get());
        $dataProvider = $this->service->search($form);

        if ($form->hasErrors()) {
            \Yii::$app->session->addFlash('error', implode(', ', $form->getFirstErrors()));
        }

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'filterModel' => $form,
        ]);
    }

    /**
     * Просмотр записи лога
     * @param int $id
     * @return string
     */
    public function actionView($id)
    {
        $model = $this->service->get($id);
        return $this->render('view', ['model' => $model]);
    }
}