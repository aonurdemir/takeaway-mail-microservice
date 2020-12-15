<?php

namespace App\Jobs;

use App\Exceptions\NoAvailableThirdPartyMailService;
use App\Models\MailJob;
use App\Services\MailSender;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

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
        try {
            $this->sendMail();
        } catch (NoAvailableThirdPartyMailService $e) {
            $this->logErrorAndReleaseJob($e);
        } catch (\Exception $e) {
            $this->logAlertAndReleaseJob($e);
        }
    }

    /**
     * @throws \App\Exceptions\NoAvailableThirdPartyMailService
     * @throws \App\Exceptions\UndefinedMailService
     */
    private function sendMail()
    {
        $mailSender = new MailSender($this->mailJob);
        $mailSender->send();
    }

    private function logErrorAndReleaseJob(NoAvailableThirdPartyMailService $e)
    {
        Log::error($e->getMessage());
        $this->release(5);
    }

    private function logAlertAndReleaseJob(\Exception $e)
    {
        Log::alert($e->getMessage());
        $this->release(30);
    }
}
