<?php

namespace glsv\smssender\interfaces;

interface ProviderMessageStatusInterface
{
    /**
     * @return string
     */
    public function getSenderStatus();
}