<?php


namespace App\Services;


use App\Models\MailJob;

interface MailService
{
    public function send(MailJob $mail);

    public function getThirdPartyProviderName();
}