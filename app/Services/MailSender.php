<?php


namespace App\Services;


use App\Exceptions\MailProviderRequestException;
use App\Exceptions\NoAvailableMailProvider;
use App\Factories\MailProviderFactory;
use App\Models\Mail;
use Exception;
use Illuminate\Support\Facades\Log;

class MailSender
{
    private Mail          $mail;
    private ?MailProvider $mailProvider;
    private array         $mailProvidersQueue;
    private bool                   $isMailSent;

    /**
     * MailSender constructor.
     *
     * @param \App\Models\Mail $mail
     *
     * @throws \App\Exceptions\UndefinedMailProvider
     */
    public function __construct(Mail $mail)
    {
        $this->mail = $mail;
        $this->mailProvidersQueue = MailProviderFactory::createAllProviders();
        $this->isMailSent = false;
    }

    /**
     * @throws \App\Exceptions\NoAvailableMailProvider
     */
    public function send(): void
    {
        $this->setMailProviderByPollingFromQueue();
        while ($this->isMailProviderSet() && ! $this->isMailSent()) {
            $this->trySendingMail();
        }

        if (! $this->isMailSent()) {
            $this->setMailJobAsFailed();

            throw new NoAvailableMailProvider();
        }
    }

    private function setMailProviderByPollingFromQueue(): void
    {
        $this->mailProvider = array_shift($this->mailProvidersQueue);
    }

    private function trySendingMail(): void
    {
        try {
            $this->sendAndSetMailAndMailJobAsSent();
        } catch (MailProviderRequestException $e) {
            $this->setMailProviderByPollingFromQueue();
        } catch (Exception $e) {
            Log::error($e->getMessage());
            $this->setMailProviderByPollingFromQueue();
        }
    }

    private function setMailJobAsFailed(): void
    {
        $this->mail->setAsFailed();
        $this->mail->save();
    }

    private function isMailProviderSet(): bool
    {
        return $this->mailProvider != null;
    }

    /**
     * @throws \App\Exceptions\MailProviderRequestException
     */
    private function sendAndSetMailAndMailJobAsSent(): void
    {
        $this->mailProvider->send($this->mail);
        $this->setMailJobAsSent();
        $this->setMailAsSent();
    }

    private function setMailJobAsSent(): void
    {
        $this->mail->setSenderThirdPartyProviderName($this->mailProvider->getName());
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