<?php


namespace App\Services;


use App\Exceptions\TypeException;
use App\Exceptions\UnsentMail;
use App\Models\MailJob;
use SendGrid;
use SendGrid\Mail\Mail as SendGridMail;

class MailServiceSendGridImp implements MailService
{
    /**
     * @var SendGrid
     */
    private SendGrid $sendGrid;

    public static function create()
    {
        return new static();
    }

    private function __construct()
    {
        $this->sendGrid = new SendGrid(config('mail.mailers.sendgrid.api_key'));
    }

    /**
     * @param \App\Models\MailJob $mailJob
     *
     * @throws \App\Exceptions\TypeException
     * @throws \App\Exceptions\UnsentMail
     */
    public function send(MailJob $mailJob)
    {
        $email = $this->prepareSendGridMail($mailJob);

        $response = $this->sendGrid->send($email);
        if ($response->statusCode() >= 300) {
            throw new UnsentMail();
        }
    }

    /**
     * @param \App\Models\MailJob $mailJob
     *
     * @return \SendGrid\Mail\Mail
     * @throws \App\Exceptions\TypeException
     */
    private function prepareSendGridMail(MailJob $mailJob)
    {
        $email = new SendGridMail();
        try {
            $email->setFrom($mailJob->from);
            $email->setSubject($mailJob->subject);
            $email->addTo($mailJob->to);
            $email->addContent("text/html", $mailJob->content);
        } catch (SendGrid\Mail\TypeException $e) {
            throw new TypeException($e->getMessage(), $e->getCode(), $e->getPrevious());
        }

        return $email;
    }

    public function getThirdPartyProviderName()
    {
        return 'sendgrid';
    }
}