<?php

namespace App\Jobs;

use App\Exceptions\NoAvailableMailProvider;
use App\Factories\MailSenderFactory;
use App\Models\Mail;
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

    public $tries = 10;

    private Mail              $mail;
    private MailSender        $mailSender;
    private MailSenderFactory $mailSenderFactory;

    public function __construct(Mail $mail)
    {
        $this->mail = $mail;
    }


    public function backoff()
    {
        $backoffSeconds = pow(2, $this->attempts());

        Log::info("Job backed off by ". $backoffSeconds);

        return $backoffSeconds;
    }

    public function handle(MailSenderFactory $mailSenderFactory)
    {
        $this->mailSenderFactory = $mailSenderFactory;
        $this->createMailSender();
        $this->sendMail();
    }

    private function createMailSender()
    {
        $this->mailSender = $this->mailSenderFactory->create($this->mail);
    }

    private function sendMail()
    {
        try {
            $this->mailSender->send();
        } catch (NoAvailableMailProvider $e) {
            Log::error($e->getMessage());
        } catch (\Exception $e) {
            Log::alert($e->getMessage());
        }
    }
}
