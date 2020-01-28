<?php

namespace glsv\smssender\models;

/**
 * Class Recipient
 * @package glsv\smssender\models
 *
 * Recipient object of a message in your accounting system
 */
class Recipient
{
    private $id;
    private $name;

    public function __construct($name, $id = null)
    {
        if (empty($name)) {
            throw new \InvalidArgumentException('The name of recipient can`t be empty.');
        }

        $this->name = $name;
        $this->id = (string)$id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }
}