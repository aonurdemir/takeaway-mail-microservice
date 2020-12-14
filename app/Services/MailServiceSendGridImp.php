<?php


namespace App\Services;


use App\Exceptions\TypeException;
use App\Models\Mail;
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
     * @param \App\Models\Mail $mail
     *
     * @throws \App\Exceptions\TypeException
     */
    public function send(Mail $mail)
    {
        $email = $this->prepareSendGridMail($mail);
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
     * @param \App\Models\Mail $mail
     *
     * @return \SendGrid\Mail\Mail
     * @throws \App\Exceptions\TypeException
     */
    private function prepareSendGridMail(Mail $mail)
    {
        $email = new SendGridMail();
        try {
            $email->setFrom($mail->from);
            $email->setSubject($mail->subject);
            $email->addTo($mail->to);
            $email->addContent("text/html", $mail->content);
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