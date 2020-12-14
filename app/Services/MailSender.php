<?php


namespace App\Services;


use App\Models\MailJob;

class MailSender
{
    private MailService $mailService;

    public function __construct()
    {
        $this->mailService = MailServiceMailjetImp::ofVersion('v3.1');
    }

    /**
     * @param \App\Models\MailJob $mail
     *
     * @throws \App\Exceptions\TypeException
     */
    public function send(MailJob $mail)
    {
        $this->mailService->send($mail);

        //todo after successful send, mark state
        $mail->markAsSent();
        $mail->setSenderThirdPartyProviderName($this->mailService->getThirdPartyProviderName());
        $mail->save();
    }

}