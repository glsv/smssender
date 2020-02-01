<?php

use PHPUnit\Framework\TestCase;
use glsv\smssender\providers\smsAero\SmsAeroProvider;
use glsv\smssender\interfaces\SendResponseInterface;

/**
 * Class SmsAeroProviderRealTest
 *
 * Тестирование реальной отправки
 * Для работы в .env файл должны быть добавлены параметры
 * SMS_AERO_USER, SMS_AERO_API_KEY, PHONE
 */
class SmsAeroProviderRealTest extends TestCase
{
    /**
     * @var SmsAeroProvider
     */
    private $provider;

    private $phone = '79200001122';

    protected function setUp()
    {
        $dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
        $dotenv->load();

        $this->phone = getenv('PHONE');

        if (empty($this->phone)) {
            $this->markTestSkipped('PHONE doesn`t exist in .env');
        }

        $user = getenv('SMS_AERO_USER');
        $api_key = getenv('SMS_AERO_API_KEY');

        if (empty($user) || empty($api_key)) {
            $this->markTestSkipped('SMS_AERO_USER or SMS_AERO_API_KEY doesn`t exist in .env');
        }

        $config = [
            'user' => $user,
            'api_key' => $api_key,
            'debug_mode' => true,
        ];

        $provider = new SmsAeroProvider($config);
        $this->provider = $provider;
    }

    public function testGetBalance()
    {
        $value = $this->provider->getBalance();
        $this->assertTrue(is_float($value));
    }

    public function testSend()
    {
        $responce = $this->provider->send($this->phone, 'message');
        $this->assertInstanceOf(SendResponseInterface::class, $responce);
    }
}