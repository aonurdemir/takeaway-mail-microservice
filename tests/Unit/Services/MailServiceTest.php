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
        $mock = $this->mockMailRepositoryReceiveCreateReturnMail($attributes, $mockMail);

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
        $mock = $this->mockMailRepositoryReceiveCreateReturnMail($attributes, $mockMail);

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
        $mock = $this->mockMailRepositoryDontReceiveCreate();

        $service = new MailService($mock);
        $this->expectException(ValidationException::class);
        $service->create($attributes);

        Bus::assertNotDispatched(SendMailJob::class);
    }

    public function test_wrong_to_argument()
    {
        $attributes = $attributes = [
            'to'   => 'some-non-email',
            'from' => 'some-non-email',
        ];

        /** @var MailRepository $mock */
        $mock = $mock = $this->mockMailRepositoryDontReceiveCreate();

        $service = new MailService($mock);
        $this->expectException(ValidationException::class);
        $service->create($attributes);

        Bus::assertNotDispatched(SendMailJob::class);
    }

    public function test_long_subject()
    {
        $attributes = $attributes = [
            'to'      => 'to@mail.com',
            'from'    => 'from@mail.com',
            'subject' => $this->createStringWithLength(79),
        ];

        /** @var MailRepository $mock */
        $mock = $mock = $this->mockMailRepositoryDontReceiveCreate();

        $service = new MailService($mock);
        $this->expectException(ValidationException::class);
        $service->create($attributes);

        Bus::assertNotDispatched(SendMailJob::class);
    }

    private function createStringWithLength(int $length)
    {
        $string = "";
        for ($i = 0; $i < $length; $i++) {
            $string .= "a";
        }

        return $string;
    }

    private function mockMailRepositoryReceiveCreateReturnMail($receiveArgs, $returnMail)
    {
        return $this->mock(
            MailRepository::class,
            function (MockInterface $mock) use ($receiveArgs, $returnMail) {
                $mock->shouldReceive('create')->once()->with($receiveArgs)
                     ->andReturn($returnMail);
            }
        );
    }

    private function mockMailRepositoryDontReceiveCreate()
    {
        return $this->mock(
            MailRepository::class,
            function (MockInterface $mock) {
                $mock->shouldNotReceive('create');
            }
        );
    }
}
