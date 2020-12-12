<?php


namespace App\Services;


use App\Models\Mail;

interface MailService
{
    public function send(Mail $mail);

}