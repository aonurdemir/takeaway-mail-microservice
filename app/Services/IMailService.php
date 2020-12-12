<?php


namespace App\Services;


use App\Models\Mail;

interface IMailService
{
    public function send(Mail $mail);

}