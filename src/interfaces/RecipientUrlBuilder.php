<?php

namespace glsv\smssender\interfaces;

interface RecipientUrlBuilder
{
    /**
     * @param $recipient_id
     * @return string|array
     */
    public function getUrl($recipient_id);
}