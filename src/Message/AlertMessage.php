<?php

namespace App\Message;

class AlertMessage
{
    public function __construct(public string $telephone, public string $message) 
    {}
}