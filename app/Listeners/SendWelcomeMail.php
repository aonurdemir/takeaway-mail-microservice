<?php

namespace App\Listeners;

use Ackintosh\Ganesha;
use Ackintosh\Ganesha\Builder;
use Ackintosh\Ganesha\GuzzleMiddleware;
use Ackintosh\Ganesha\Storage\Adapter\Redis;
use App\Events\CustomerRegistered;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendWelcomeMail implements ShouldQueue
{
    use InteractsWithQueue;

    private Ganesha $circuitBreaker;
    private \Redis  $redisClient;
    private Client  $guzzleClient;

    public $tries = 3;

    public function __construct(\Redis $redisClient)
    {
        $this->redisClient = $redisClient;
    }

    public function backoff()
    {
        Log::debug("backing off ".($this->attempts() * 2));

        return $this->attempts() * 10;
    }

    /**
     * @param \App\Events\CustomerRegistered $event
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Exception
     */
    public function handle(CustomerRegistered $event)
    {
        Log::debug("trying ".$this->attempts()." times");

        $this->connectRedisService();
        $this->createCircuitBreaker();
        $this->createGuzzleClient();
        $this->sendMail($event->getEmail());

        Log::debug("\n");

    }

    private function createCircuitBreaker()
    {
        $this->circuitBreaker = Builder::withRateStrategy()
                                       ->adapter(new Redis($this->redisClient))
                                       ->failureRateThreshold(50)
                                       ->intervalToHalfOpen(10)
                                       ->minimumRequests(10)
                                       ->timeWindow(30)
                                       ->build();
    }

    private function connectRedisService()
    {
        $this->redisClient->connect('redis');
    }

    private function createGuzzleClient()
    {
        $middleware = new GuzzleMiddleware($this->circuitBreaker);

        $handlers = HandlerStack::create();
        $handlers->push($middleware);

        $this->guzzleClient = new Client(['handler' => $handlers]);
    }

    /**
     * @param string $email
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Exception
     */
    private function sendMail(string $email)
    {
        Log::debug("sending to {$email}");

        $response = $this->guzzleClient->request(
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

        Log::debug($response->getStatusCode());
        Log::debug($response->getReasonPhrase());
    }


}
