<?php


namespace App\Services;


use App\Models\MailJob;

interface MailService
{
    /**
     * @param \App\Models\MailJob $mailJob
     *
     * @throws \App\Exceptions\MailNotSent
     */
    public function send(MailJob $mailJob);

    public function getThirdPartyProviderName(): string;
}