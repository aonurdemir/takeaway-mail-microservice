<?php


namespace App\Exceptions;


use Exception;

class MailProviderRequestException extends Exception
{
    private string $providerName;
    private int    $statusCode;

    public function __construct(string $providerName, int $statusCode)
    {
        parent::__construct();

        $this->providerName = $providerName;
        $this->statusCode = $statusCode;
    }

    public function getProviderName(): string
    {
        return $this->providerName;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}