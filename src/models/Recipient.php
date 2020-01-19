<?php

namespace glsv\smssender\models;

class Recipient
{
    private $id;
    private $name;

    public function __construct($name, $id = null)
    {
        if (empty($name)) {
            throw new \InvalidArgumentException('Имя должно быть указано.');
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