<?php

namespace App\Listeners;

use App\Events\CustomerRegistered;
use App\Exceptions\MailProviderResponseException;
use App\Network\CircuitBreakingGuzzleClient;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendWelcomeMail implements ShouldQueue
{
    use InteractsWithQueue;

    public $tries = 3;

    private CircuitBreakingGuzzleClient $circuitBreakingGuzzleClient;

    public function __construct(CircuitBreakingGuzzleClient $circuitBreakingGuzzleClient)
    {
        $this->circuitBreakingGuzzleClient = $circuitBreakingGuzzleClient;
    }

    public function backoff()
    {
        return pow(2, $this->attempts());
    }

    /**
     * @param \App\Events\CustomerRegistered $event
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function handle(CustomerRegistered $event)
    {
        try {
            $this->sendMail($event->getEmail());
        } catch (Exception $e) {
            Log::error($e->getMessage(), $e->getTrace());
            $this->release($this->backoff());
        }
    }

    /**
     * @param string $email
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Exception
     */
    private function sendMail(string $email)
    {
        $response = $this->circuitBreakingGuzzleClient->request(
            'POST',
            config('services.inhouse_mail_service.url'),
            [
                'json' =>
                    [
                        'from'    => 'aaonurdemir@gmail.com',
                        'to'      => $email,
                        'subject' => 'Welcome to Takeaway',
                        'content' => 'Here are facts about Takeaway',
                    ],
            ]
        );

        if ($response->getStatusCode() >= 300) {
            Log::error($response->getReasonPhrase());

            throw new MailProviderResponseException('inhouse', $response->getStatusCode());
        }
    }


}
