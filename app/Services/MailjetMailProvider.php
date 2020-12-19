<?php


namespace App\Services;


use App\Exceptions\MailProviderRequestException;
use App\Models\Mail;
use Illuminate\Support\Facades\Log;
use Mailjet\Client;
use Mailjet\Resources;
use Mailjet\Response;

class MailjetMailProvider implements MailProvider
{
    private Client $mailjetClient;

    public function __construct(Client $mailjetClient)
    {
        $this->mailjetClient = $mailjetClient;
    }

    /**
     * @param \App\Models\Mail $mail
     *
     * @throws \App\Exceptions\MailProviderRequestException
     */
    public function send(Mail $mail)
    {
        $body = $this->prepareMailjetMailBody($mail);
        $response = $this->mailjetClient->post(Resources::$Email, ['body' => $body]);

        if (! $response->success()) {
            $this->logErrorOfUnsuccessfulResponse($response);
            throw new MailProviderRequestException($this->getName(), $response->getStatus());
        }
    }

    public function getName(): string
    {
        return 'mailjet';
    }

    private function logErrorOfUnsuccessfulResponse(Response $response)
    {
        Log::error(
            'Unsuccessful response from Mailjet mail service provider',
            ['context' => $response->getReasonPhrase()]
        );
    }

    /**
     * @param \App\Models\Mail $mail
     *
     * @return array
     */
    private function prepareMailjetMailBody(Mail $mail)
    {
        return [
            'Messages' => [
                [
                    'From'     => [
                        'Email' => $mail->from,
                        'Name'  => "Abdullah Onur",
                    ],
                    'To'       => [
                        [
                            'Email' => $mail->to,
                            'Name'  => "Abdullah Onur",
                        ],
                    ],
                    'Subject'  => $mail->subject,
                    //'TextPart' => "My first Mailjet email",
                    'HTMLPart' => $mail->content,
                ],
            ],
        ];
    }
}