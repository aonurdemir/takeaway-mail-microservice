<?php

namespace Tests\Unit\Services\Utils;

use App\Exceptions\NoSuchProviderException;
use App\Factories\MailProviderFactory;
use App\Services\Utils\MailProviderIterator;
use Tests\TestCase;

class MailProviderIteratorTest extends TestCase
{
    public function test_empty_iterator_has_next()
    {
        $iterator = new MailProviderIterator([]);

        $this->assertFalse($iterator->hasNext());
    }

    public function test_next_of_empty_iterator()
    {
        $iterator = new MailProviderIterator([]);

        $this->expectException(NoSuchProviderException::class);
        $iterator->next();
    }

    /**
     * @throws \App\Exceptions\UndefinedMailProvider
     */
    public function test_has_next_of_iterator_with_one_provider()
    {
        $providers = [MailProviderFactory::create(MailProviderFactory::SENDGRID)];
        $iterator = new MailProviderIterator($providers);

        $this->assertTrue($iterator->hasNext());
    }

    /**
     * @throws \App\Exceptions\NoSuchProviderException
     * @throws \App\Exceptions\UndefinedMailProvider
     */
    public function test_next_of_iterator_with_one_provider()
    {
        $providers = [MailProviderFactory::create(MailProviderFactory::SENDGRID)];
        $iterator = new MailProviderIterator($providers);

        $provider = $iterator->next();

        $this->assertEquals($providers[0]->getName(), $provider->getName());
    }


    /**
     * @throws \App\Exceptions\NoSuchProviderException
     * @throws \App\Exceptions\UndefinedMailProvider
     */
    public function test_2_next_of_iterator_with_one_provider()
    {
        $providers = [MailProviderFactory::create(MailProviderFactory::SENDGRID)];
        $iterator = new MailProviderIterator($providers);

        $iterator->next();
        $this->expectException(NoSuchProviderException::class);
        $iterator->next();
    }

    /**
     * @throws \App\Exceptions\NoSuchProviderException
     * @throws \App\Exceptions\UndefinedMailProvider
     */
    public function test_has_next_after_next_of_iterator_with_one_provider()
    {
        $providers = [MailProviderFactory::create(MailProviderFactory::SENDGRID)];
        $iterator = new MailProviderIterator($providers);

        $iterator->next();
        $this->assertFalse($iterator->hasNext());
    }

    /**
     * @throws \App\Exceptions\NoSuchProviderException
     * @throws \App\Exceptions\UndefinedMailProvider
     */
    public function test_2_next_of_iterator_with_two_provider()
    {
        $providers = [
            MailProviderFactory::create(MailProviderFactory::SENDGRID),
            MailProviderFactory::create(MailProviderFactory::MAILJET),
        ];
        $iterator = new MailProviderIterator($providers);
        $iterator->next();
        $provider = $iterator->next();
        $this->assertEquals($providers[1]->getName(), $provider->getName());
    }

}