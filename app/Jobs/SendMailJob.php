<?php

namespace App\Jobs;

use App\Exceptions\NoAvailableMailProvider;
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

    public function __construct(Mail $mail)
    {
        $this->mail = $mail;
    }

    public function backoff()
    {
        return pow(2, $this->attempts());
    }

    public function handle(MailSender $mailSender)
    {
        $this->mailSender = $mailSender;
        $this->mailSender->setMail($this->mail);
        $this->trySendingOrRelease();
    }

    private function trySendingOrRelease()
    {
        try {
            $this->mailSender->send();
        } catch (NoAvailableMailProvider $e) {
            Log::error($e->getMessage());
            Log::info("Job released with backoff ".$this->backoff());
            $this->release($this->backoff());
        }
    }
}
