<?php

namespace App\Exceptions;

class MailProviderResponseException extends MailProviderRequestException
{
    public function __construct(string $providerName, int $statusCode)
    {
        parent::__construct("{$providerName} provider returned status code {$statusCode}");
    }
}