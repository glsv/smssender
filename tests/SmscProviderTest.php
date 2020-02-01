<?php

use PHPUnit\Framework\TestCase;
use glsv\smssender\exceptions\ValidateException;
use glsv\smssender\providers\smsc\SmscProvider;
use glsv\smssender\interfaces\SendResponseInterface;
use glsv\smssender\interfaces\SmsLogModelInterface;
use glsv\smssender\providers\smsc\api\SmscApi;

class SmscProviderTest extends TestCase
{
    /**
     * @var SmscProvider
     */
    private $provider;

    private $phone = '7920001122';

    protected function setUp()
    {
        $config = [
            'login' => 'login',
            'psw' => 'password',
        ];

        $provider = new SmscProvider($config);

        /**
         * Меняем api провайдера на stub
         */
        $api = $this->createMock(SmscApi::class);
        $api->method('send')->willReturn(json_encode(['id' => 1, 'cnt' => '1']));

        $last_timestamp = time();

        $api->method('getStatus')->willReturn(json_encode([
            'status' => 1,
            'last_date' => date('d.m.Y H:i:s', $last_timestamp),
            'last_timestamp' => $last_timestamp
        ]));

        $api->method('getBalance')->willReturn(json_encode(['balance' => 20.01]));

        $refObj = new ReflectionClass($provider);

        $property = $refObj->getProperty('api');
        $property->setAccessible(true);
        $property->setValue($provider, $api);

        $this->provider = $provider;
    }

    public function testGetKey()
    {
        $this->assertNotEmpty($this->provider->getKey());
    }

    public function testEmptyPhoneSend()
    {
        $this->expectException(ValidateException::class);
        $this->provider->send('', 'message');
    }

    public function testEmptyMessageSend()
    {
        $this->expectException(ValidateException::class);
        $this->provider->send($this->phone, '');
    }

    /**
     * Проверка баланса
     */
    public function testGetBalance()
    {
        $value = $this->provider->getBalance();
        $this->assertTrue(is_float($value));
    }

    /**
     * Отправить сообщение
     */
    public function testSend()
    {
        $result = $this->provider->send($this->phone, 'message');
        $this->assertInstanceOf(SendResponseInterface::class, $result);
    }

    /**
     * Проверка статуса
     */
    public function testStatus()
    {
        $smsLog = $this->createMock(SmsLogModelInterface::class);
        $smsLog->method('getPhone')->willReturn($this->phone);
        $smsLog->method('getMessageId')->willReturn(1);

        $result = $this->provider->getInfoMessage($smsLog);

        $this->assertInstanceOf(SendResponseInterface::class, $result);
    }
}