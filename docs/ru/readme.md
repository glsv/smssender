# Интерфейс просмотра логов
Интерфейс просмотра сохраняемых логов sms-сообщений следует рассматрировать в качестве шаблона,
который вы можете откорректировать под ваши потребности.

### Базовый интерфейс
Базовый интерфейс просмотра логов подключается через модуль в конфигурации приложения.
```
'modules' => [
    'sms-sender' => [
        'class' => 'glsv\smssender\module\Module',
        'controllerNamespace' => 'glsv\smssender\module\controllers',
        'defaultRoute' => 'sms-log/index'
    ],
]
```
В этом случае доступ будет по адресу: http://your-site.com/sms-sender/

### Кастомизация интерфейса 

Например, если необходимо запретить просмотр логов неавторизованным пользователям и 
кастомизировать views, можно сделать следующим образом.

На примере yii2-advanced и размещения интерфейса по адресу http://your-site.com/sms-log/

1. Переместь файлы views в `backend\views\sms-log\` и откорректировать их под ваши потребности.
2. В контроллер, наследованный от `glsv\smssender\module\controllers\SmsLogController`, 
добавить фильтр контроля доступа.  

```
<?php

namespace backend\controllers;

use glsv\smssender\module\controllers\SmsLogController as BaseSmsLogController;
use yii\filters\AccessControl;

class SmsLogController extends BaseSmsLogController
{
    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }
}
```

### Описание контроллера
Базовый контроллер реализует следующую функциональность: 
- Cписок логов через ActiveDataProvider (index action);
- Просмотр одной записи лога (view action);
- Обновить статус лога через запрос к sms-провайдеру (update-status action);
- Проверка баланса sms-провайдера (check-balance action);
- Форма и отправка sms-сообщение (form action).
  
```
class SmsLogController extends Controller
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
            ],
            // Проверка баланса sms-провайдера и вывод его в session Flash
            'check-balance' => [
                'class' => CheckBalanceAction::class,
            ],
            // Простая форма отправки sms
            'form' => [
                'class' => SendSimpleFormAction::class,
            ]
        ];
    }

    /**
     * Список sms-логов через DataProvider и GridView
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
     */
    public function actionView($id)
    {
        $model = $this->service->get($id);
        return $this->render('view', ['model' => $model]);
    }
}
```

### GridView
Для отображения списка логов предназначен `glsv\smssender\widgets\SmsLogGridView`.
Он наследуется от стандартного `yii\grid\GridView` с уже настроенными колонками 
и дополнительными параметрами.
```
<?= SmsLogGridView::widget([
    'dataProvider' => $dataProvider,
    // 'recipientVisible' => false,
    // 'recipientUrlBuilder' => new YourRecipientUrlBuilder(),
]) ?
```
- `recipientVisible` - отключает отображение колонки с именем получателя. 
Опция нужна когда список sms отображается на странице самого получателя в админ панели.
- `recipientUrlBuilder` - модель наследованная от `glsv\smssender\interfaces\RecipientUrlBuilder`.
Используется когда необходимо не только показать имя получателя, но и дать ссылку на его страницу.
`RecipientUrlBuilder` должен реализовывать метод `getUrl($recipient_id)` 
