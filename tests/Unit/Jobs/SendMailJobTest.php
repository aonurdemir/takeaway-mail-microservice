<?php

namespace Jobs;

use App\Jobs\SendMailJob;
use App\Models\Mail;
use Illuminate\Queue\Jobs\RedisJob;
use Mockery\MockInterface;
use Tests\TestCase;


class SendMailJobTest extends TestCase
{
    public function test_backoff_after_first_attempt()
    {
        /** @var RedisJob $mockRedisJob */
        $mockRedisJob = $mockRedisJob = $this->mockRedisJobReceiveAttemptsReturn(1);

        $job = new SendMailJob(new Mail());
        $job->setJob($mockRedisJob);

        $this->assertEquals(2, $job->backoff());
    }

    public function test_backoff_after_second_attempt()
    {
        /** @var RedisJob $mockRedisJob */
        $mockRedisJob = $this->mockRedisJobReceiveAttemptsReturn(2);

        $job = new SendMailJob(new Mail());
        $job->setJob($mockRedisJob);

        $this->assertEquals(4, $job->backoff());
    }

    public function test_backoff_after_tenth_attempt()
    {
        /** @var RedisJob $mockRedisJob */
        $mockRedisJob = $this->mockRedisJobReceiveAttemptsReturn(10);

        $job = new SendMailJob(new Mail());
        $job->setJob($mockRedisJob);

        $this->assertEquals(1024, $job->backoff());
    }

    private function mockRedisJobReceiveAttemptsReturn(int $count)
    {
        return $this->mock(
            RedisJob::class,
            function (MockInterface $mock) use ($count) {
                $mock->shouldReceive('attempts')->once()
                     ->andReturn($count);
            }
        );
    }
}
