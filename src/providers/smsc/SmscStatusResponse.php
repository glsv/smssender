<?php

namespace glsv\smssender\providers\smsc;

use glsv\smssender\interfaces\ProviderMessageStatusInterface;
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
     * @var int
     */
    private $last_timestamp;

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

        $required = ['status', 'last_timestamp'];

        foreach ($required as $attr) {
            if (!isset($response[$attr])) {
                throw new \InvalidArgumentException('The "response" param must contain a "' . $attr . '" value.');
            }
        }

        $this->message_id = $message_id;
        $this->raw_response = $response;
        $this->last_timestamp = $response['last_timestamp'];
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
     * @return ProviderMessageStatusInterface
     */
    public function getProviderStatus(): ProviderMessageStatusInterface
    {
        return $this->statusModel;
    }

    /**
     * @return array
     */
    public function getResponse()
    {
        return $this->raw_response;
    }

    /**
     * @return int timestamp
     */
    public function getDateLastChangeStatus()
    {
        return $this->last_timestamp ?? time();
    }
}