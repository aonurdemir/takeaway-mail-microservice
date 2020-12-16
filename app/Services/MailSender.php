<?php


namespace App\Services;


use App\Exceptions\MailNotSent;
use App\Exceptions\NoAvailableThirdPartyMailService;
use App\Models\MailJob;
use Exception;
use Illuminate\Support\Facades\Log;

class MailSender
{
    private MailJob      $mailJob;
    private ?MailService $mailService;
    private array        $mailServiceQueue;
    private bool         $isMailSent;

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
        $this->isMailSent = false;
    }

    /**
     * @throws \App\Exceptions\NoAvailableThirdPartyMailService
     */
    public function send(): void
    {
        $this->setMailServiceByPollingFromQueue();
        while ($this->mailServiceAvailableAndMailNotSent()) {
            $this->trySendingMail();
        }

        if (! $this->isMailSent()) {
            $this->setMailJobAsFailed();
        }

        throw new NoAvailableThirdPartyMailService();
    }

    private function setMailServiceByPollingFromQueue(): void
    {
        $this->mailService = array_shift($this->mailServiceQueue);
    }

    private function mailServiceAvailableAndMailNotSent(): bool
    {
        return $this->isMailServiceSet() && ! $this->isMailSent();
    }

    private function trySendingMail(): void
    {
        try {
            $this->sendAndSetMailAndMailJobAsSent();
        }
        catch (MailNotSent $e){
            $this->setMailServiceByPollingFromQueue();
        }
        catch (Exception $e) {
            Log::error($e->getMessage());
            $this->setMailServiceByPollingFromQueue();
        }
    }

    private function setMailJobAsFailed(): void
    {
        $this->mailJob->setAsFailed();
        $this->mailJob->save();
    }

    private function isMailServiceSet(): bool
    {
        return $this->mailService != null;
    }

    /**
     * @throws \App\Exceptions\MailNotSent
     */
    private function sendAndSetMailAndMailJobAsSent(): void
    {
        $this->mailService->send($this->mailJob);
        $this->setMailJobAsSent();
        $this->setMailAsSent();
    }

    private function setMailJobAsSent(): void
    {
        $this->mailJob->setSenderThirdPartyProviderName($this->mailService->getThirdPartyProviderName());
        $this->mailJob->setAsSent();
        $this->mailJob->save();
    }

    private function setMailAsSent(): void
    {
        $this->isMailSent = true;
    }

    private function isMailSent(): bool
    {
        return $this->isMailSent;
    }
}