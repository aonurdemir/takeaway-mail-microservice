<?php


namespace App\Services;


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
     * @throws \App\Exceptions\UnsentMail
     * @throws \SendGrid\Mail\TypeException
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
     * @throws \SendGrid\Mail\TypeException
     */
    private function prepareSendGridMail(MailJob $mailJob)
    {
        $email = new SendGridMail();
        $email->setFrom($mailJob->from);
        $email->setSubject($mailJob->subject);
        $email->addTo($mailJob->to);
        $email->addContent("text/html", $mailJob->content);


        return $email;
    }

    public function getThirdPartyProviderName()
    {
        return 'sendgrid';
    }
}