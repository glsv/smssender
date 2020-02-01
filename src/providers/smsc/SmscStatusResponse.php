<?php

namespace glsv\smssender\providers\smsc;

use glsv\smssender\interfaces\SendResponseInterface;

class SmscStatusResponse implements SendResponseInterface
{
    /**
     * @var int
     */
    private $message_id;

    /**
     * @var int
     */
    private $status;

    /**
     * @var array
     */
    private $raw_response;

    /**
     * @var SmscMessageStatus
     */
    private $statusModel;

    /**
     * SmscSendResponse constructor.
     * @param int $message_id
     * @param array $response
     */
    public function __construct($message_id, $response)
    {
        if (!is_int($message_id)) {
            throw new \InvalidArgumentException('The "message_id" param must be a "int".');
        }

        if (!isset($response['status'])) {
            throw new \InvalidArgumentException('The "raw_response" param must contain a "status" value.');
        }

        $this->message_id = $message_id;
        $this->raw_response = $response;
        $this->status = $response['status'];
        $this->statusModel = new SmscMessageStatus($this->status);
    }

    /**
     * @return int
     */
    public function getMessageId()
    {
        return $this->message_id;
    }

    /**
     * @return string
     */
    public function getMessageStatus()
    {
        return $this->statusModel->getSenderStatus();
    }

    /**
     * @return string
     */
    public function getProviderStatus()
    {
        $this->statusModel->getStatus();
    }

    /**
     * @return string
     */
    public function getProviderStatusLabel()
    {
        $this->statusModel->getLabel();
    }

    /**
     * @return array
     */
    public function getResponse()
    {
        return $this->raw_response;
    }
}