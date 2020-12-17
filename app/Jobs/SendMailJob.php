<?php

namespace App\Jobs;

use App\Exceptions\NoAvailableThirdPartyMailService;
use App\Models\MailJob;
use App\Services\MailSender;
use App\Services\MailSenderFactory;
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

    private MailJob           $mailJob;
    private MailSender        $mailSender;
    private MailSenderFactory $mailSenderFactory;

    public function __construct(MailJob $mailJob)
    {
        $this->mailJob = $mailJob;
    }

    public function handle(MailSenderFactory $mailSenderFactory)
    {
        $this->mailSenderFactory = $mailSenderFactory;
        $this->createMailSender();
        $this->sendMail();
    }

    private function createMailSender()
    {
        $this->mailSender = $this->mailSenderFactory->create($this->mailJob);
    }

    private function sendMail()
    {
        try {
            $this->mailSender->send();
        } catch (NoAvailableThirdPartyMailService $e) {
            Log::error($e->getMessage());
        } catch (\Exception $e) {
            Log::alert($e->getMessage());
        }
    }
}
