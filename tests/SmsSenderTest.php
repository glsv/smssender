<?php

use PHPUnit\Framework\TestCase;
use glsv\smssender\interfaces\SendResponseInterface;
use glsv\smssender\vo\SendMethod;
use glsv\smssender\exceptions\ValidateException;
use glsv\smssender\SmsSender;

class SmsSenderTest extends TestCase
{
    /**
     * @var \glsv\smssender\interfaces\SmsSenderInterface
     */
    private $sender;

    private $phone = '79200000000';

    protected function setUp()
    {
        $provider = $this->createMock(\glsv\smssender\providers\smsAero\SmsAeroProvider::class);
        $service = new \glsv\smssender\services\SmsLogService();
        $this->sender = new SmsSender($provider, $service);
        $this->sender->logDisable();
    }

    public function testFakeSend()
    {
        $this->sender->fakeModeEnable();
        $response = $this->sender->send($this->phone, 'message', SendMethod::METHOD_MANUAL);

        $this->assertInstanceOf(SendResponseInterface::class, $response, 'Ответ должен быть формата SendResponseInterface');
    }

    public function testSendEmptyMessage()
    {
        $this->expectException(ValidateException::class);
        $this->sender->send($this->phone, '', SendMethod::METHOD_MANUAL);
    }

    public function testSendWrongNumber()
    {
        $this->expectException(ValidateException::class);
        $this->sender->send('92000000', 'message', SendMethod::METHOD_MANUAL);
    }

    public function testSendWrongMethod()
    {
        $this->expectException(ValidateException::class);
        $this->sender->send($this->phone, 'message', 'wrongMethod');
    }
}