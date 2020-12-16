<?php

namespace App\Services;

use App\Models\MailJob;

class MailSenderFactory
{
    public function create(MailJob $mailJob): MailSender
    {
        return new MailSender($mailJob);
    }
}