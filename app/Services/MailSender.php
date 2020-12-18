<?php


namespace App\Services;


use App\Exceptions\MailProviderRequestException;
use App\Exceptions\NoAvailableMailProvider;
use App\Exceptions\NoSuchProviderException;
use App\Factories\MailProviderFactory;
use App\Models\Mail;
use Illuminate\Support\Facades\Log;

class MailSender
{
    private Mail                 $mail;
    private ?MailProvider        $mailProvider;
    private MailProviderIterator $mailProviderIterator;

    /**
     * MailSender constructor.
     *
     * @param \App\Models\Mail $mail
     *
     */
    public function __construct(Mail $mail)
    {
        $this->mail = $mail;
        $this->mailProviderIterator = MailProviderFactory::getIterator();
    }

    /**
     * @throws \App\Exceptions\NoAvailableMailProvider
     */
    public function send(): void
    {
        try {
            $this->tryAllProvidersUntilSent();
        } catch (NoSuchProviderException $e) {
            Log::info("All mail providers failed to send the mail: ".$this->mail->id);
            $this->fail();
        }
    }

    /**
     * @throws \App\Exceptions\NoAvailableMailProvider
     */
    private function fail()
    {
        $this->mail->setAsFailed();
        $this->mail->save();

        throw new NoAvailableMailProvider();
    }

    /**
     * @throws \App\Exceptions\NoSuchProviderException
     */
    private function tryAllProvidersUntilSent()
    {
        while (! $this->mail->isSent()) {
            $this->mailProvider = $this->mailProviderIterator->next();
            $this->trySendingMail();
        }
    }

    private function trySendingMail()
    {
        try {
            $this->mailProvider->send($this->mail);
            $this->setMailAsSent();
        } catch (MailProviderRequestException $e) {
            Log::error($e->getMessage());
        }
    }

    private function setMailAsSent(): void
    {
        $this->mail->setSenderThirdPartyProviderName($this->mailProvider->getName());
        $this->mail->setAsSent();
        $this->mail->save();
    }
}