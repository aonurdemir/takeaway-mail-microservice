<?php


namespace App\Exceptions;


use Exception;

class NoAvailableThirdPartyMailService extends Exception
{

    /**
     * MailCannotBeSent constructor.
     */
    public function __construct()
    {
        parent::__construct("No third party mail services is available");
    }
}