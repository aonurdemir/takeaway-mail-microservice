<?php


namespace App\Services;


use App\Models\Mail;

interface MailProvider
{
    /**
     * @param \App\Models\Mail $mail
     *
     * @throws \App\Exceptions\MailProviderRequestException
     */
    public function send(Mail $mail);

    public function getName(): string;
}