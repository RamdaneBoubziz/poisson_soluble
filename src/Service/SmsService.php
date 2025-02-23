<?php

namespace App\Service;

use Psr\Log\LoggerInterface;

class SmsService
{
    public function __construct(private LoggerInterface $logger)
    {
    }

    public function sendSms(string $phone, string $message): void
    {
        $this->logger->info('SMS envoyé au' . $phone . ' : ' . $message);
    }
}
