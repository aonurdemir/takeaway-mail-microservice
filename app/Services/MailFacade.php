<?php


namespace App\Services;


use App\Models\Mail;

class MailFacade
{
    private MailService $mailService;

    public function __construct()
    {
        $this->mailService = new SendGridMailServiceImp();
    }

    /**
     * @param \App\Models\Mail $mail
     *
     * @throws \App\Exceptions\TypeException
     */
    public function send(Mail $mail)
    {
        $this->mailService->send($mail);

        //todo after successful send, mark state
        $mail->markAsSent();
        $mail->save();
    }

}