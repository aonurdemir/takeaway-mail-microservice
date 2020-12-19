<?php


namespace App\Services;


use App\Exceptions\MailProviderRequestException;
use App\Models\Mail;
use Illuminate\Support\Facades\Log;
use SendGrid;
use SendGrid\Mail\Mail as SendGridMail;
use SendGrid\Response;

class SendGridMailProvider implements MailProvider
{
    private SendGrid $sendGrid;

    public function __construct(SendGrid $sendGrid)
    {
        $this->sendGrid = $sendGrid;
    }

    /**
     * @param \App\Models\Mail $mail
     *
     * @throws \App\Exceptions\MailProviderRequestException
     * @throws \SendGrid\Mail\TypeException
     */
    public function send(Mail $mail)
    {
        $email = $this->prepareSendGridMail($mail);

        $response = $this->sendGrid->send($email);
        if ($response->statusCode() >= 300) {
            $this->logErrorOfUnsuccessfulResponse($response);
            throw new MailProviderRequestException($this->getName(), $response->statusCode());
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

    public function getName(): string
    {
        return 'sendgrid';
    }
}