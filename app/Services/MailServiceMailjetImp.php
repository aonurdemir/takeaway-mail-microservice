<?php


namespace App\Services;


use App\Models\MailJob;
use Mailjet\Client;
use Mailjet\Resources;

class MailServiceMailjetImp implements MailService
{
    /**
     * @var \Mailjet\Client
     */
    private Client $mailjetClient;

    public static function ofVersion(string $version)
    {
        return new static($version);
    }

    private function __construct($version)
    {
        $this->mailjetClient = $mj = new Client(
            config('mail.mailers.mailjet.key'),
            config('mail.mailers.mailjet.secret'),
            true,
            ['version' => $version]
        );
    }

    /**
     * @param \App\Models\MailJob $mailJob
     *
     */
    public function send(MailJob $mailJob)
    {
        $body = $this->prepareMailjetMailBody($mailJob);
        $response = $this->mailjetClient->post(Resources::$Email, ['body' => $body]);

        var_dump($response->success());
        var_dump($response->getData());
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

    public function getThirdPartyProviderName()
    {
        return 'mailjet';
    }
}