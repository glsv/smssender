<?php

namespace glsv\smssender\providers\smsc\api;

/**
 * Class SmscApi
 * @package glsv\smssender\providers\smsc\api
 *
 * API к сервису Smsc.ru для выполнения базовых операций
 */
class SmscApi
{
    CONST FORMAT_STRING = 0;
    CONST FORMAT_DIGIT = 1;
    CONST FORMAT_XML = 2;
    CONST FORMAT_JSON = 3;

    public static $formats = [
        self::FORMAT_STRING,
        self::FORMAT_DIGIT,
        self::FORMAT_XML,
        self::FORMAT_JSON,
    ];

    /**
     * @var string
     */
    private $login;

    /**
     * @var string Пароль
     */
    private $psw;

    /**
     * @var string Имя отправителя
     */
    private $sender;

    /**
     * @var string
     */
    private $baseUrl = 'https://smsc.ru/sys/';

    private $format = self::FORMAT_JSON;

    public function __construct($login, $psw){
        $this->login = $login;
        $this->psw = $psw;
    }

    public function setFormat($format)
    {
        if (!in_array($format, static::$formats)) {
            throw new \InvalidArgumentException('format value is wrong.');
        }

        $this->format = $format;
    }

    /**
     * @param string|array $phones
     * @param $message
     * @return mixed
     */
    public function send($phones, $message)
    {
        if (empty($phones)) {
            throw new \RuntimeException('phones can`t be empty.');
        }

        if (is_string($phones)) {
            $phones = [$phones];
        }

        if (empty($message)) {
            throw new \RuntimeException('The message can`t be empty.');
        }

        if (strlen($message) > 1000) {
            throw new \RuntimeException('The message can`t be more than 1000 characters.');
        }

        $params = [
            'phones' => implode(';', $phones),
            'mes' => $message,
        ];

        return $this->call('send', $params);
    }

    /**
     * @param mixed $message_id
     * @param string $phone
     * @return mixed
     */
    public function getStatus($message_id, $phone)
    {
        if (empty($message_id)) {
            throw new \RuntimeException('The "message_id" param can`t be empty.');
        }

        if (empty($phone)) {
            throw new \RuntimeException('The "phone" param can`t be empty.');
        }

        $params = [
            'id' => $message_id,
            'phone' => $phone,
        ];

        return $this->call('status', $params);
    }

    /**
     * @return mixed
     */
    public function getBalance()
    {
        return $this->call('balance');
    }

    /**
     * @return array
     */
    protected function getRequiredParams()
    {
        return [
            'login' => $this->login,
            'psw' => $this->psw,
            'fmt' => $this->format,
        ];
    }

    /**
     * @param string $method
     * @param array $params
     * @return mixed
     */
    public function call($method, array $params = [])
    {
        $params = array_merge($this->getRequiredParams(), $params);
        $url = $this->baseUrl . $method . '.php?' . http_build_query($params);

        $response = $this->sendCurl($url);
        return $response;
    }

    /**
     * @param string $url
     * @param array $options
     * @return mixed
     */
    private function sendCurl($url, array $options = [])
    {
        $defaults = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_SSL_VERIFYHOST => 0,
        ];

        $ch = curl_init();

        curl_setopt_array($ch, $defaults + $options);

        if (!$result = curl_exec($ch)) {
            $error = 'code: ' . curl_errno($ch) . ', message: ' . curl_error($ch);
            curl_close($ch);
            throw new SmscApiException('CURL ERROR: ' . $error);
        }

        curl_close($ch);

        return $result;
    }
}