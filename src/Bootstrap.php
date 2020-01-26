<?php

namespace glsv\smssender;

use yii\base\BootstrapInterface;

class Bootstrap implements BootstrapInterface
{
    public function bootstrap($app)
    {
        $app->i18n->translations['sms-sender*'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'basePath' => '@vendor/glsv/yii2-smssender/src/messages',
            'fileMap' => [
                'sms-sender/app' => 'app.php',
                'sms-sender/statuses' => 'statuses.php',
            ]
        ];
    }
}