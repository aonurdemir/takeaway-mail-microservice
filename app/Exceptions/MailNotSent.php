<?php


namespace App\Exceptions;


class MailNotSent extends \Exception
{

    /**
     * MailNotSent constructor.
     */
    public function __construct()
    {
        parent::__construct("Mail cannot be sent");
    }
}