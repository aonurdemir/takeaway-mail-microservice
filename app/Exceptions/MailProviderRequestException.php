<?php


namespace App\Exceptions;


use Exception;

class MailProviderRequestException extends Exception
{
    public function __construct(string $providerName, int $statusCode)
    {
        parent::__construct("Provider {$providerName} returned status code {$statusCode}");
    }
}