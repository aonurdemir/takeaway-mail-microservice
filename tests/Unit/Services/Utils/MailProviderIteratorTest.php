<?php

namespace Tests\Unit\Services\Utils;

use App\Exceptions\NoSuchProviderException;
use App\Services\SendGridMailProvider;
use App\Services\Utils\MailProviderIterator;
use Mockery\MockInterface;
use Tests\TestCase;

class MailProviderIteratorTest extends TestCase
{
    public function test_empty_iterator_has_next()
    {
        $iterator = new MailProviderIterator();
        $this->assertFalse($iterator->hasNext());
    }

    public function test_next_of_empty_iterator()
    {
        $iterator = new MailProviderIterator();

        $this->expectException(NoSuchProviderException::class);
        $iterator->next();
    }

    /**
     */
    public function test_has_next_of_iterator_with_one_provider()
    {
        /** @var SendGridMailProvider $mockProvider */
        $mockProvider = $this->mock(SendGridMailProvider::class);
        $iterator = new MailProviderIterator($mockProvider);

        $this->assertTrue($iterator->hasNext());
    }

    /**
     * @throws \App\Exceptions\NoSuchProviderException
     */
    public function test_next_of_iterator_with_one_provider()
    {
        /** @var SendGridMailProvider $mockProvider */
        $mockProvider = $this->mock(
            SendGridMailProvider::class,
            function (MockInterface $mock) {
                $mock->shouldReceive('getName')->once()
                     ->andReturn('sendGrid');
            }
        );

        $iterator = new MailProviderIterator($mockProvider);
        $provider = $iterator->next();

        $this->assertEquals('sendGrid', $provider->getName());
    }


    /**
     * @throws \App\Exceptions\NoSuchProviderException
     */
    public function test_2_next_of_iterator_with_one_provider()
    {
        /** @var SendGridMailProvider $mockProvider */
        $mockProvider = $this->mock(SendGridMailProvider::class);

        $iterator = new MailProviderIterator($mockProvider);
        $iterator->next();
        $this->expectException(NoSuchProviderException::class);
        $iterator->next();
    }

    /**
     * @throws \App\Exceptions\NoSuchProviderException
     */
    public function test_has_next_after_next_of_iterator_with_one_provider()
    {
        /** @var SendGridMailProvider $mockProvider */
        $mockProvider = $this->mock(SendGridMailProvider::class);
        $iterator = new MailProviderIterator($mockProvider);

        $iterator->next();
        $this->assertFalse($iterator->hasNext());
    }

    /**
     * @throws \App\Exceptions\NoSuchProviderException
     */
    public function test_2_next_of_iterator_with_two_provider()
    {
        /** @var SendGridMailProvider $mockSendGridProvider */
        $mockSendGridProvider = $this->mock(SendGridMailProvider::class);

        /** @var SendGridMailProvider $mockMailjetProvider */
        $mockMailjetProvider = $this->mock(
            SendGridMailProvider::class,
            function (MockInterface $mock) {
                $mock->shouldReceive('getName')->once()
                     ->andReturn('mailjet');
            }
        );
        $iterator = new MailProviderIterator($mockSendGridProvider, $mockMailjetProvider);
        $iterator->next();
        $provider = $iterator->next();
        $this->assertEquals('mailjet', $provider->getName());
    }

}