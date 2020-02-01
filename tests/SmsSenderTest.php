<?php

use PHPUnit\Framework\TestCase;
use glsv\smssender\vo\SendMethod;
use glsv\smssender\exceptions\ValidateException;
use glsv\smssender\SmsSender;
use glsv\smssender\interfaces\SmsLogModelInterface;
use glsv\smssender\interfaces\SmsProviderInterface;
use glsv\smssender\models\DebugSendResponse;
use glsv\smssender\interfaces\SendResponseInterface;
use glsv\smssender\interfaces\SmsFormInterface;

class SmsSenderTest extends TestCase
{
    /**
     * @var \glsv\smssender\interfaces\SmsSenderInterface
     */
    private $sender;

    private $phone = '79200000000';

    protected function setUp()
    {
        /**
         * Заменяем провайдера на stub
         */
        $provider = $this->createMock(SmsProviderInterface::class);
        $provider->method('getKey')->willReturn('test');
        $provider->method('getBalance')->willReturn(20.11);
        $provider->method('send')->willReturn(new DebugSendResponse());
        $provider->method('getInfoMessage')->willReturn(new DebugSendResponse());

        $service = new \glsv\smssender\services\SmsLogService();

        $this->sender = new SmsSender($provider, $service);
        $this->sender->logDisable();
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

    public function testSend()
    {
        $response = $this->sender->send($this->phone, 'message', SendMethod::METHOD_MANUAL);
        $this->assertInstanceOf(SendResponseInterface::class, $response);
    }

    public function testGetInfoMessage()
    {
        $smsLog = $this->createMock(SmsLogModelInterface::class);
        $smsLog->method('getPhone')->willReturn($this->phone);
        $smsLog->method('getMessageId')->willReturn(1);

        $response = $this->sender->getInfoMessage($smsLog);
        $this->assertInstanceOf(SendResponseInterface::class, $response);
    }

    public function testSendByForm()
    {
        $form = $this->createMock(SmsFormInterface::class);
        $form->method('getNumber')->willReturn($this->phone);
        $form->method('getMessage')->willReturn('message');

        $response = $this->sender->sendByForm($form);
        $this->assertInstanceOf(SendResponseInterface::class, $response);
    }

    public function testGetBalance()
    {
        $this->assertTrue(is_float($this->sender->getBalance()));
    }

    public function testGetCurrentProviderKey()
    {
        $this->assertNotEmpty($this->sender->getCurrentProviderKey());
    }

    public function testGetLastSmsLog()
    {
        $this->sender->getLastSmsLog();
    }

    public function testfakeModeEnable()
    {
        $this->sender->fakeModeEnable();

        $refObj = new ReflectionClass($this->sender);
        $property = $refObj->getProperty('fake_mode');
        $property->setAccessible(true);

        $this->assertTrue($property->getValue($this->sender));
    }

    public function testfakeModeDisable()
    {
        $refObj = new ReflectionClass($this->sender);
        $property = $refObj->getProperty('fake_mode');
        $property->setAccessible(true);
        $property->setValue($this->sender, false);

        $this->sender->fakeModeDisable();

        $this->assertFalse($property->getValue($this->sender));
    }

    public function testlogDisable()
    {
        $this->sender->logDisable();

        $refObj = new ReflectionClass($this->sender);
        $property = $refObj->getProperty('log_enabled');
        $property->setAccessible(true);

        $this->assertFalse($property->getValue($this->sender));
    }
}