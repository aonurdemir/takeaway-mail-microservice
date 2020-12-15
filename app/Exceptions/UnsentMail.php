<?php


namespace App\Exceptions;


class UnsentMail extends \Exception
{

    /**
     * UnsentMail constructor.
     */
    public function __construct()
    {
        parent::__construct("Mail cannot be sent");
    }
}