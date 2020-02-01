<?php

use PHPUnit\Framework\TestCase;
use glsv\smssender\providers\smsc\api\SmscApi;

/**
 * Class SmscApiTest
 *
 * Тестирование api Smsc.ru c реальной отправкой
 */
class SmscApiTest extends TestCase
{
    /**
     * @var SmscApi
     */
    private $api;

    private $phone;

    public function setUp()
    {
        $dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
        $dotenv->load();

        $this->phone = getenv('PHONE');

        if (empty($this->phone)) {
            $this->markTestSkipped('PHONE doesn`t exist in .env');
        }

        $login = getenv('SMSC_LOGIN');
        $password = getenv('SMSC_PASSWORD');

        if (empty($login) || empty($password)) {
            $this->markTestSkipped('SMSC_LOGIN or SMSC_PASSWORD don`t exist in .env');
        }

        $this->api = new SmscApi($login, $password);
        $this->api->setFormat(SmscApi::FORMAT_JSON);
    }


    public function testBalance()
    {
        $response = $this->api->getBalance();
        $result = json_decode($response, true);

        $this->assertTrue(isset($result['balance']));
    }

    public function testSend()
    {
        $response = $this->api->send($this->phone, 'message');
        $result = json_decode($response, true);

        $this->assertTrue(isset($result['id']));

        return $result['id'];
    }

    /**
     * @depends testSend
     */
    public function testStatus($message_id)
    {
        $response = $this->api->getStatus($message_id, $this->phone);
        $result = json_decode($response, true);

        $this->assertTrue(isset($result['status']));
    }
}