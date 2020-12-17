<?php

namespace App\Services;

use App\Models\Mail;

class MailSenderFactory
{
    public function create(Mail $mail): MailSender
    {
        return new MailSender($mail);
    }
}