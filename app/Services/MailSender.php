<?php


namespace App\Services;


use App\Models\MailJob;

class MailSender
{
    private MailService $mailService;
    private MailJob     $mailJob;

    /**
     * MailSender constructor.
     *
     * @param \App\Models\MailJob $mailJob
     *
     * @throws \App\Exceptions\UndefinedMailService
     */
    public function __construct(MailJob $mailJob)
    {
        $this->mailJob = $mailJob;
        $this->mailService = MailServiceFactory::create(MailServiceFactory::MAILJET);
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