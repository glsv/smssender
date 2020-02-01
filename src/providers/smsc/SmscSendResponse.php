<?php

namespace glsv\smssender\providers\smsc;

use glsv\smssender\interfaces\SendResponseInterface;

class SmscSendResponse implements SendResponseInterface
{
    /**
     * @var int
     */
    private $message_id;

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
     * @param array $response
     */
    public function __construct($response)
    {
        if (!isset($response['id'])) {
            throw new \InvalidArgumentException('The "response" param must contain a "id" value.');
        }

        $this->raw_response = $response;
        $this->message_id = $response['id'];
        $this->statusModel = new SmscMessageStatus(SmscMessageStatus::STATUS_QUEUE);
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
        $this->statusModel->getSenderStatus();
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