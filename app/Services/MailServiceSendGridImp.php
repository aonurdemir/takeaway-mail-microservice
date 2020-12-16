<?php


namespace App\Services;


use App\Exceptions\MailNotSent;
use App\Models\MailJob;
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
     * @param \App\Models\MailJob $mailJob
     *
     * @throws \App\Exceptions\MailNotSent
     * @throws \SendGrid\Mail\TypeException
     */
    public function send(MailJob $mailJob)
    {
        $email = $this->prepareSendGridMail($mailJob);

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

    public function getThirdPartyProviderName(): string
    {
        return 'sendgrid';
    }
}