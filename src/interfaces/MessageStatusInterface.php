<?php


namespace glsv\smssender\interfaces;

interface MessageStatusInterface
{
    public function getStatus();

    /**
     * @return string
     */
    public function getLabel();
}