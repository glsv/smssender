<?php

namespace glsv\smssender\interfaces;

interface RecipientUrlBuilder
{
    public function getUrl($recipient_id);
}