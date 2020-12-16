<?php


namespace App\Services;


use App\Exceptions\MailNotSent;
use App\Models\Mail;
use Illuminate\Support\Facades\Log;
use Mailjet\Client;
use Mailjet\Resources;
use Mailjet\Response;

class MailServiceMailjetImp implements MailService
{
    /**
     * @var \Mailjet\Client
     */
    private Client $mailjetClient;

    private function __construct($version)
    {
        $this->mailjetClient = $mj = new Client(
            config('services.mailjet.key'),
            config('services.mailjet.secret'),
            true,
            ['version' => $version]
        );
    }

    public static function ofVersion(string $version)
    {
        return new static($version);
    }

    /**
     * @param \App\Models\Mail $mail
     *
     * @throws \App\Exceptions\MailNotSent
     */
    public function send(Mail $mail)
    {
        $body = $this->prepareMailjetMailBody($mail);
        $response = $this->mailjetClient->post(Resources::$Email, ['body' => $body]);

        if (! $response->success()) {
            $this->logErrorOfUnsuccessfulResponse($response);
            throw new MailNotSent();
        }
    }

    public function getThirdPartyProviderName(): string
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