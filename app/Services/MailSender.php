<?php


namespace App\Services;


use App\Exceptions\NoAvailableThirdPartyMailService;
use App\Models\MailJob;
use Exception;
use Illuminate\Support\Facades\Log;

class MailSender
{
    private MailJob      $mailJob;
    private ?MailService $mailService;
    private array        $mailServiceQueue;

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
        $this->mailServiceQueue = MailServiceFactory::createAllServices();
    }

    /**
     * @throws \App\Exceptions\NoAvailableThirdPartyMailService
     */
    public function send()
    {
        $this->setMailServiceFromQueue();
        while ($this->isMailServiceAvailable()) {
            try {
                $this->doSend();
                $this->markMailJobAsSent();

                return;
            } catch (Exception $e) {
                Log::error($e->getMessage());
                $this->setMailServiceFromQueue();
            }
        }
        $this->markMailJobAsFailed();

        throw new NoAvailableThirdPartyMailService();
    }

    private function setMailServiceFromQueue()
    {
        $this->mailService = array_shift($this->mailServiceQueue);
    }

    private function doSend()
    {
        $this->mailService->send($this->mailJob);
    }

    private function isMailServiceAvailable(): bool
    {
        return $this->mailService != null;
    }

    private function markMailJobAsSent()
    {
        $this->mailJob->setSenderThirdPartyProviderName($this->mailService->getThirdPartyProviderName());
        $this->mailJob->markAsSent();
        $this->mailJob->save();
    }

    private function markMailJobAsFailed()
    {
        $this->mailJob->markAsFailed();
        $this->mailJob->save();
    }
}