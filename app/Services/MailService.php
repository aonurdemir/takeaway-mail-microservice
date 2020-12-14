<?php


namespace App\Services;


use App\Models\MailJob;

interface MailService
{
    public function send(MailJob $mailJob);

    public function getThirdPartyProviderName();
}