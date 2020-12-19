<?php


namespace App\Network;


use Ackintosh\Ganesha;
use Ackintosh\Ganesha\GuzzleMiddleware;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use Psr\Http\Message\ResponseInterface;

class CircuitBreakingGuzzleClient
{
    private Ganesha $circuitBreaker;
    private Client  $guzzleClient;

    public function __construct(Ganesha $circuitBreaker)
    {
        $this->circuitBreaker = $circuitBreaker;
        $this->initGuzzleClient();
    }

    private function initGuzzleClient()
    {
        $middleware = new GuzzleMiddleware($this->circuitBreaker);

        $handlers = HandlerStack::create();
        $handlers->push($middleware);

        $this->guzzleClient = new Client(['handler' => $handlers]);
    }

    /**
     * @param string $method
     * @param string $uri
     * @param array  $options
     *
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function request(string $method, $uri = '', array $options = []): ResponseInterface
    {
        return $this->guzzleClient->request($method, $uri, $options);
    }
}