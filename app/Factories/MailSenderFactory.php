<?php

namespace App\Factories;

use App\Models\Mail;
use App\Services\MailSender;

class MailSenderFactory
{
    public function create(Mail $mail): MailSender
    {
        return new MailSender($mail);
    }
}