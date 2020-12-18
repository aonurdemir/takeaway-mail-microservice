<?php

namespace Tests\Unit\Services;

use App\Jobs\SendMailJob;
use App\Models\Mail;
use App\Repositories\MailRepository;
use App\Services\MailService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Validation\ValidationException;
use Mockery\MockInterface;
use Tests\Utils\MailTestBase;

class MailServiceTest extends MailTestBase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        Bus::fake();
    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public function test_main_scenario()
    {
        /** @var Mail $mockMail */
        $mockMail = Mail::factory()->make();
        $attributes = $attributes = [
            'to'      => $mockMail->to,
            'from'    => $mockMail->from,
            'subject' => $mockMail->subject,
            'content' => $mockMail->content,
        ];

        /** @var MailRepository $mock */
        $mock = $this->mock(
            MailRepository::class,
            function (MockInterface $mock) use ($attributes, $mockMail) {
                $mock->shouldReceive('create')->once()->with($attributes)
                     ->andReturn($mockMail);
            }
        );

        $service = new MailService($mock);
        $createdMail = $service->create($attributes);

        Bus::assertDispatched(SendMailJob::class);
        $this->assertMailsEqual($mockMail, $createdMail);
    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public function test_without_optional_args()
    {
        /** @var Mail $mockMail */
        $mockMail = Mail::factory()->onlyRequired()->make();
        $attributes = $attributes = [
            'to'   => $mockMail->to,
            'from' => $mockMail->from,
        ];

        /** @var MailRepository $mock */
        $mock = $this->mock(
            MailRepository::class,
            function (MockInterface $mock) use ($attributes, $mockMail) {
                $mock->shouldReceive('create')->once()->with($attributes)
                     ->andReturn($mockMail);
            }
        );

        $service = new MailService($mock);
        $createdMail = $service->create($attributes);

        Bus::assertDispatched(SendMailJob::class);
        $this->assertMailsEqual($mockMail, $createdMail);
    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public function test_without_required_args()
    {
        $attributes = $attributes = [
            'subject' => 'some subject',
            'content' => 'some content',
        ];

        /** @var MailRepository $mock */
        $mock = $this->mock(
            MailRepository::class,
            function (MockInterface $mock) {
                $mock->shouldNotReceive('create');
            }
        );

        $service = new MailService($mock);

        $this->expectException(ValidationException::class);
        $service->create($attributes);

        Bus::assertNotDispatched(SendMailJob::class);
    }
}
