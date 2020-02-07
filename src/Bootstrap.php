<?php

namespace glsv\smssender;

use yii\base\BootstrapInterface;

class Bootstrap implements BootstrapInterface
{
    public function bootstrap($app)
    {
        $app->controllerMap['migrate-sms'] = [
            'class' => 'yii\console\controllers\MigrateController',
            'migrationNamespaces' => [
                'glsv\smssender\migrations'
            ],
        ];

        $app->i18n->translations['sms-sender*'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'basePath' => '@vendor/glsv/yii2-smssender/src/messages',
            'fileMap' => [
                'sms-sender/app' => 'app.php',
                'sms-sender/statuses' => 'statuses.php',
                'sms-sender/messages' => 'messages.php',
            ]
        ];
    }
}