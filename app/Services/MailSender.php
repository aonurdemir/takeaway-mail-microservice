<?php


namespace App\Services;


use App\Exceptions\MailNotSent;
use App\Exceptions\NoAvailableThirdPartyMailService;
use App\Factories\ThirdPartyMailServiceFactory;
use App\Models\Mail;
use Exception;
use Illuminate\Support\Facades\Log;

class MailSender
{
    private Mail                   $mail;
    private ?ThirdPartyMailService $thirdPartyMailService;
    private array                  $thirdPartyMailServiceQueue;
    private bool                   $isMailSent;

    /**
     * MailSender constructor.
     *
     * @param \App\Models\Mail $mail
     *
     * @throws \App\Exceptions\UndefinedMailService
     */
    public function __construct(Mail $mail)
    {
        $this->mail = $mail;
        $this->thirdPartyMailServiceQueue = ThirdPartyMailServiceFactory::createAllServices();
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

            throw new NoAvailableThirdPartyMailService();
        }
    }

    private function setMailServiceByPollingFromQueue(): void
    {
        $this->thirdPartyMailService = array_shift($this->thirdPartyMailServiceQueue);
    }

    private function mailServiceAvailableAndMailNotSent(): bool
    {
        return $this->isMailServiceSet() && ! $this->isMailSent();
    }

    private function trySendingMail(): void
    {
        try {
            $this->sendAndSetMailAndMailJobAsSent();
        } catch (MailNotSent $e) {
            $this->setMailServiceByPollingFromQueue();
        } catch (Exception $e) {
            Log::error($e->getMessage());
            $this->setMailServiceByPollingFromQueue();
        }
    }

    private function setMailJobAsFailed(): void
    {
        $this->mail->setAsFailed();
        $this->mail->save();
    }

    private function isMailServiceSet(): bool
    {
        return $this->thirdPartyMailService != null;
    }

    /**
     * @throws \App\Exceptions\MailNotSent
     */
    private function sendAndSetMailAndMailJobAsSent(): void
    {
        $this->thirdPartyMailService->send($this->mail);
        $this->setMailJobAsSent();
        $this->setMailAsSent();
    }

    private function setMailJobAsSent(): void
    {
        $this->mail->setSenderThirdPartyProviderName($this->thirdPartyMailService->getName());
        $this->mail->setAsSent();
        $this->mail->save();
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