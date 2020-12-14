<?php

namespace App\Jobs;

use App\Models\MailJob;
use App\Services\MailSender;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendMailJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * @var \App\Models\MailJob
     */
    private $mailJob;

    /**
     * Create a new job instance.
     *
     * @param \App\Models\MailJob $mailJob
     */
    public function __construct(MailJob $mailJob)
    {
        $this->mailJob = $mailJob;
    }

    /**
     * Execute the job.
     *
     * @param \App\Services\MailSender $mailSender
     *
     * @return void
     * @throws \App\Exceptions\TypeException
     */
    public function handle(MailSender $mailSender)
    {
        $mailSender->send($this->mailJob);
    }
}
