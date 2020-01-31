# Интерфейс просмотра логов
Расширение не имеет готового интерфейса просмотра сохраняемых логов, 
но содержит ряд компонентов из которых можно собрать контроллер с интерфейсом. 
Сам интерфейс (view-файлы) нужно реализовать самостоятельно.

### Контроллер
Пример реализации контроллера. Реализует следующую функциональность: 
- Cписок логов через ActiveDataProvider (index action)
- Просмотр одной записи лога (view action)
- Обновить статус лога через запрос к sms-провайдеру (update-status action)
- Проверка баланса sms-провайдера (check-balance action)
- Форма и отправка sms-сообщение (form action)
  
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

### Шаблоны
Базовые шаблоны контроллера и views можно взять из [template](../template) 
