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
     * @param \App\Models\MailJob $mail
     *
     */
    public function send(MailJob $mail)
    {
        $body = $this->prepareMailjetMailBody($mail);
        $response = $this->mailjetClient->post(Resources::$Email, ['body' => $body]);

        var_dump($response->success());
        var_dump($response->getData());
    }

    /**
     * @param \App\Models\MailJob $mail
     *
     * @return array
     */
    private function prepareMailjetMailBody(MailJob $mail)
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

    public function getThirdPartyProviderName()
    {
        return 'mailjet';
    }
}