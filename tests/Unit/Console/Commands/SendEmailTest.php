<?php

namespace Tests\Unit\Console\Commands;

use App\Services\MailService;
use Illuminate\Validation\ValidationException;
use Mockery\MockInterface;
use Symfony\Component\Console\Exception\RuntimeException;
use Tests\TestCase;

class SendEmailTest extends TestCase
{
    public function testMainScenario()
    {
        $this->mock(
            MailService::class,
            function (MockInterface $mock) {
                $mock->shouldReceive('create')->once();
            }
        );

        $this->artisan(
            'mail:send',
            [
                'from'      => 'from@takeaway.com',
                'to'        => 'to@takeaway.com',
                '--subject' => 'Mail Subject',
                '--content' => 'Hello from test',
            ]
        )->expectsOutput("Successful");
    }

    public function testWithoutSubject()
    {
        $this->mock(
            MailService::class,
            function (MockInterface $mock) {
                $mock->shouldReceive('create')->once();
            }
        );

        $this->artisan(
            'mail:send',
            [
                'from'      => 'from@takeaway.com',
                'to'        => 'to@takeaway.com',
                '--content' => 'Hello from test',
            ]
        )->expectsOutput("Successful");
    }

    public function testWithoutContent()
    {
        $this->mock(
            MailService::class,
            function (MockInterface $mock) {
                $mock->shouldReceive('create')->once();
            }
        );

        $this->artisan(
            'mail:send',
            [
                'from'      => 'from@takeaway.com',
                'to'        => 'to@takeaway.com',
                '--subject' => 'Mail Subject',
            ]
        )->expectsOutput("Successful");
    }

    public function testWithoutTo()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Not enough arguments (missing: "to")');

        $this->artisan(
            'mail:send',
            [
                'from'      => 'from@takeaway.com',
                '--subject' => 'Mail Subject',
                '--content' => 'Hello from test',
            ]
        );
    }

    public function testWithoutFrom()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Not enough arguments (missing: "from")');

        $this->artisan(
            'mail:send',
            [
                'to'        => 'to@takeaway.com',
                '--subject' => 'Mail Subject',
                '--content' => 'Hello from test',
            ]
        );
    }

    public function testWithInvalidArgs()
    {
        $this->mock(
            MailService::class,
            function (MockInterface $mock) {
                $mock->shouldReceive('create')->once()->andThrow(
                    ValidationException::class,
                    "The given data was invalid."
                );
            }
        );

        $this->artisan(
            'mail:send',
            [
                'from'      => 'from-takeaway.com',
                'to'        => 'to@takeaway.com',
                '--subject' => 'Mail Subject',
                '--content' => 'Hello from test',
            ]
        )->expectsOutput("The given data was invalid.");
    }
}
