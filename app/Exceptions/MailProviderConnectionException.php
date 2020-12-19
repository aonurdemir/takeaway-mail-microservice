<?php

namespace App\Exceptions;

class MailProviderConnectionException extends MailProviderRequestException
{
    public function __construct(\Exception $e)
    {
        parent::__construct(
            $e->getMessage(),
            $e->getCode(),
            $e->getPrevious()
        );
    }
}