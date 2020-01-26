# Интерфейс просмотра логов smsSender
Расширение не имеет готового интерфейса просмотра сохраняемых логов, 
но содержит ряд компонентов из которых можно собрать контроллер с интерфейсом для работы с логами. 
Сам интерфейс (view-файлы) нужно реализовать самостоятельно.

**Пример контроллера** 
```
use glsv\smssender\actions\CheckBalanceAction;
use glsv\smssender\actions\SendSimpleFormAction;
use glsv\smssender\actions\UpdateStatusAction;
use glsv\smssender\forms\SmsLogSearch;
use glsv\smssender\interfaces\SmsSenderInterface;
use glsv\smssender\services\SmsLogService;

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

    public function actionView($id)
    {
        $model = $this->service->get($id);
        return $this->render('view', ['model' => $model]);
    }
}
```

## Шаблоны
Базовые шаблоны контроллера и views можно взять из [template](../template) 
