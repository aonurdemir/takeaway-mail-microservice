<?php


namespace App\Services;


use App\Exceptions\MailNotSent;
use App\Models\Mail;
use Illuminate\Support\Facades\Log;
use SendGrid;
use SendGrid\Mail\Mail as SendGridMail;
use SendGrid\Response;

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
        $this->sendGrid = new SendGrid(config('services.sendgrid.api_key'));
    }

    /**
     * @param \App\Models\Mail $mail
     *
     * @throws \App\Exceptions\MailNotSent
     * @throws \SendGrid\Mail\TypeException
     */
    public function send(Mail $mail)
    {
        $email = $this->prepareSendGridMail($mail);

        $response = $this->sendGrid->send($email);
        if ($response->statusCode() >= 300) {
            $this->logErrorOfUnsuccessfulResponse($response);
            throw new MailNotSent();
        }
    }

    private function logErrorOfUnsuccessfulResponse(Response $response)
    {
        Log::error(
            'Unsuccessful response from Sendgrid mail service provider',
            ['context' => $response->body()]
        );
    }

    /**
     * @param \App\Models\Mail $mail
     *
     * @return \SendGrid\Mail\Mail
     * @throws \SendGrid\Mail\TypeException
     */
    private function prepareSendGridMail(Mail $mail)
    {
        $email = new SendGridMail();
        $email->setFrom($mail->from);
        $email->setSubject($mail->subject);
        $email->addTo($mail->to);
        $email->addContent("text/html", $mail->content);


        return $email;
    }

    public function getThirdPartyProviderName(): string
    {
        return 'sendgrid';
    }
}