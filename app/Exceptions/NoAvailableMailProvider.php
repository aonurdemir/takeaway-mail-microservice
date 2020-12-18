<?php


namespace App\Exceptions;


use Exception;

class NoAvailableMailProvider extends Exception
{
    public function __construct()
    {
        parent::__construct("No mail providers is available");
    }
}