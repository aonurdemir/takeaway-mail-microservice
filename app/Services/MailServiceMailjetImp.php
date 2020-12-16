<?php


namespace App\Services;


use App\Exceptions\MailNotSent;
use App\Models\MailJob;
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
     * @param \App\Models\MailJob $mailJob
     *
     * @throws \App\Exceptions\MailNotSent
     */
    public function send(MailJob $mailJob)
    {
        $body = $this->prepareMailjetMailBody($mailJob);
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
     * @param \App\Models\MailJob $mailJob
     *
     * @return array
     */
    private function prepareMailjetMailBody(MailJob $mailJob)
    {
        return [
            'Messages' => [
                [
                    'From'     => [
                        'Email' => $mailJob->from,
                        'Name'  => "Abdullah Onur",
                    ],
                    'To'       => [
                        [
                            'Email' => $mailJob->to,
                            'Name'  => "Abdullah Onur",
                        ],
                    ],
                    'Subject'  => $mailJob->subject,
                    //'TextPart' => "My first Mailjet email",
                    'HTMLPart' => $mailJob->content,
                ],
            ],
        ];
    }
}