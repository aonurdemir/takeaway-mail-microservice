<?php


namespace App\Services;


use App\Exceptions\TypeException;
use App\Models\MailJob;
use SendGrid;
use SendGrid\Mail\Mail as SendGridMail;

class MailServiceSendGridImp implements MailService
{
    /**
     * @var SendGrid
     */
    private SendGrid $sendGrid;

    public function __construct()
    {
        $this->sendGrid = new SendGrid(config('mail.mailers.sendgrid.api_key'));
    }

    /**
     * @param \App\Models\MailJob $mailJob
     *
     * @throws \App\Exceptions\TypeException
     */
    public function send(MailJob $mailJob)
    {
        $email = $this->prepareSendGridMail($mailJob);
        try {
            $response = $this->sendGrid->send($email);
            var_dump($response->statusCode());
            var_dump($response->headers());
            var_dump($response->body());
            //todo return response with a service response object
        } catch (\Exception $e) {
            var_dump($e->getMessage());
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