<?php


namespace App\Services;


use App\Models\MailJob;

class MailSender
{
    private MailService $mailService;
    private MailJob     $mailJob;

    public function __construct(MailJob $mailJob)
    {
        $this->mailJob = $mailJob;
        $this->mailService = MailServiceMailjetImp::ofVersion('v3.1');
    }

    public function send()
    {
        $this->mailService->send($this->mailJob);

        //todo after successful send, mark state
        $this->mailJob->markAsSent();
        $this->mailJob->setSenderThirdPartyProviderName($this->mailService->getThirdPartyProviderName());
        $this->mailJob->save();
    }

}