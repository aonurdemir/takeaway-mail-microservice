<?php


namespace App\Exceptions;


use Exception;

class UndefinedMailService extends Exception
{

    /**
     * UndefinedMailService constructor.
     *
     * @param string $service
     */
    public function __construct(string $service)
    {
        parent::__construct("{$service} is not defined");
    }
}