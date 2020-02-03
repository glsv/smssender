<?php

namespace glsv\smssender\interfaces;

interface ProviderMessageStatusInterface
{
    public function getStatus();

    /**
     * @return string
     */
    public function getLabel();
    
    /**
     * @return string
     */
    public function getSenderStatus();
}