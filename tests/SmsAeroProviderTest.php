<?php

use PHPUnit\Framework\TestCase;
use glsv\smssender\providers\smsAero\SmsAeroProvider;

class SmsAeroProviderTest extends TestCase
{
    /**
     * @var SmsAeroProvider
     */
    private $provider;

    protected function setUp()
    {
        $dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
        $dotenv->load();

        $user = getenv('SMS_AERO_USER');
        $api_key = getenv('SMS_AERO_API_KEY');

        if (empty($user) || empty($api_key)) {
            $this->markTestSkipped('SMS_AERO_USER or SMS_AERO_API_KEY is not exist in .env');
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
        $phone = getenv('PHONE');
        $this->provider->send($phone, 'message');
    }
}