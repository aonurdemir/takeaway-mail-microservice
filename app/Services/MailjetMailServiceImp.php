<?php


namespace App\Services;


use App\Models\Mail;
use Mailjet\Client;
use Mailjet\Resources;

class MailjetMailServiceImp implements MailService
{
    /**
     * @var \Mailjet\Client
     */
    private Client $mailjetClient;

    public function __construct()
    {
        $this->mailjetClient = $mj = new Client(
            config('mail.mailers.mailjet.key'),
            config('mail.mailers.mailjet.secret'),
            true,
            ['version' => 'v3.1']
        );
    }

    /**
     * @param \App\Models\Mail $mail
     *
     */
    public function send(Mail $mail)
    {
        $body = $this->prepareMailjetMailBody($mail);
        $response = $this->mailjetClient->post(Resources::$Email, ['body' => $body]);

        var_dump($response->success());
        var_dump($response->getData());
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