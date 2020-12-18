<?php


namespace App\Exceptions;


use Exception;

class UndefinedMailProvider extends Exception
{

    /**
     * UndefinedMailService constructor.
     *
     * @param string $provider
     */
    public function __construct(string $provider)
    {
        parent::__construct("{$provider} is not defined");
    }
}