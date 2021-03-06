<?php

use PHPUnit\Framework\TestCase;
use glsv\smssender\exceptions\ValidateException;
use glsv\smssender\providers\smsAero\SmsAeroProvider;
use glsv\smssender\interfaces\SendResponseInterface;
use glsv\smssender\interfaces\SmsLogModelInterface;
use evgenyy33\smsaerov2\SmsaeroApiV2;

class SmsAeroProviderTest extends TestCase
{
    /**
     * @var SmsAeroProvider
     */
    private $provider;

    private $phone = '79200001122';

    protected function setUp()
    {
        $config = [
            'user' => 'user',
            'api_key' => 'api_key',
            'debug_mode' => true,
        ];

        $provider = new SmsAeroProvider($config);

        /**
         * Меняем api провайдера на stub
         */
        $api = $this->createMock(SmsaeroApiV2::class);

        $send_data = [
            'success' => true,
            'message' => '',
            'data' => [
                'id' => 0123456,
                'from' => 'NAME',
                'number' => '79040123344',
                'text' => 'message text',
                'status' => 8,
                'extendStatus' => 'moderation',
                'channel' => 'DIRECT',
                'cost' => 2.69,
                'dateCreate' => time(),
                'dateSend' => time(),
            ],
        ];

        $api->method('send')->willReturn($send_data);
        $api->method('test_send')->willReturn($send_data);

        $api->method('check_send')->willReturn([
            'success' => true,
            'message' => '',
            'data' => [
                'id' => 0123456,
                'from' => 'NAME',
                'number' => '79040123344',
                'text' => 'message text',
                'status' => 8,
                'extendStatus' => 'moderation',
                'channel' => 'DIRECT',
                'cost' => 2.69,
                'dateCreate' => time(),
                'dateSend' => time(),
            ]
        ]);

        $api->method('balance')->willReturn([
            'success' => true,
            'message' => '',
            'data' => [
                'balance' => 10.15,
            ],
        ]);

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