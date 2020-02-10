# SmsSender
Компонент-обертка над провайдерами отправки SMS, с логированием в БД и хелперами для построения 
интерфейса просмотра логов.

Удобен для следущей ситуации: 
* Осуществляется рассылка по большому числу получателей (подписчики, рекламодатели и т.п)
* Необходим интерфейс просмотра логов отправки
* Требуется независимость от конкретного sms-провайдера   

## Установка
```
composer require glsv/yii2-smssender "*"
```

## Использование
```
$smsSender->send($phone, $message, SendMethod::METHOD_MANUAL);
```
Сообщение будет отправлено через подключенный SMS-провайдер, а результат отправки (успешный или нет) залогирован.

## Настройка
1. [Конфигурация sms-провайдера](#setup1)
2. [Настроить внедрение зависимостей](#setup2)
3. [Настроить и выполнить миграций](#setup3)
4. [Добавить обработка callback sms-провайдера (опционально)](#setup4)

#### <a name="setup1"></a> 1. Конфигурация sms-провайдера
Поместить конфигурацию sms-провайдера в params.php. На примере _SmsAeroProvider_
```
'smsAeroProvider' => [
  'user' => 'login',
  'api_key' => 'api_key',
  'sign' => 'sign',
  'channel' => \glsv\smssender\providers\smsAero\SmsAeroProvider::CHANNEL_DIRECT,
  'callbackUrl' => 'https://site.com/sms-aero/callback/update-status',
  'debug_mode' => false,
]
``` 

#### <a name="setup2"></a> 2. BootstrapComponent для внедрения зависимостей
```
use yii\base\BootstrapInterface;
use glsv\smssender\providers\smsAero\SmsAeroProvider;
use glsv\smssender\interfaces\SmsProviderInterface;
use glsv\smssender\interfaces\SmsSenderInterface;
use glsv\smssender\SmsSender;

class BootstrapComponent implements BootstrapInterface
{
    public function bootstrap($app)
    {
        $container = \Yii::$container;

        $config = \Yii::$app->params['smsAeroProvider'];

        $container->set(SmsProviderInterface::class, SmsAeroProvider::class, [$config]);
        $container->setSingleton(SmsSenderInterface::class,  SmsSender::class);
    }
}
```

Добавить BootstrapComponent в main.php
```
'bootstrap' => [\common\components\BootstrapComponent::class],
```

#### <a name="setup3"></a> 3. Миграции
Выполнить миграции по команде 
```
php yii migrate-sms
```

#### <a name="setup4"></a> 4. Обработка callback sms-провайдера
Если нужно обрабатывать callback от sms-провайдера, то 
добавить модуль соответствующего провайдера в конфигурацию приложения.
Например, для callback следующего формата:

https://site.com/sms-aero/callback/update-status

добавить:
```
'modules' => [
    'sms-aero' => [
        'class' => 'glsv\smssender\providers\smsAero\module\Module',
        'controllerNamespace' => 'glsv\smssender\providers\smsAero\module\controllers',
    ],
],
```

#### 5. Интерфейс просмотра логов
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
Доступ будет по адресу: http://your-site.com/sms-sender/

Как правило, и сам интерфейс и контроллер требуют индивидуальной настройки. 
Подробнее можно прочитать в [описании](docs/ru/readme.md).


