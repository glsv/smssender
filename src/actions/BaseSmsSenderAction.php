<?php

namespace glsv\smssender\actions;

use glsv\smssender\interfaces\SmsSenderInterface;
use yii\base\Action;

abstract class BaseSmsSenderAction extends Action
{
    /**
     * @var SmsSenderInterface
     */
    public $sender;

    public function __construct($id, $controller, SmsSenderInterface $sender, $config = [])
    {
        parent::__construct($id, $controller, $config);
        $this->sender = $sender;
    }
}