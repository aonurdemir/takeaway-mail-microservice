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

    private MailJob $mailJob;

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
     * @return void
     */
    public function handle()
    {
        $mailSender = new MailSender($this->mailJob);
        $mailSender->send();
    }
}
