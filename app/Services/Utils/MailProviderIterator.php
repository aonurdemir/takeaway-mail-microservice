<?php

namespace App\Services\Utils;

use App\Exceptions\NoSuchProviderException;
use App\Services\MailProvider;

class MailProviderIterator
{
    /**
     * @var \App\Services\MailProvider[]
     */
    private array $providers;

    private int $currentIndex;

    /**
     * MailProviderIterator constructor.
     *
     * @param \App\Services\MailProvider[] $providers
     */
    public function __construct(MailProvider ...$providers)
    {
        $this->providers = $providers;
        $this->currentIndex = 0;
    }

    /**
     * @return \App\Services\MailProvider
     * @throws \App\Exceptions\NoSuchProviderException
     */
    public function next()
    {
        if (! $this->hasNext()) {
            throw new NoSuchProviderException();
        }

        $provider = $this->getCurrentProvider();
        $this->incrementIndex();

        return $provider;
    }

    public function hasNext()
    {
        return $this->currentIndex < count($this->providers);
    }

    private function getCurrentProvider()
    {
        return $this->providers[$this->currentIndex];
    }

    private function incrementIndex()
    {
        $this->currentIndex++;
    }
}