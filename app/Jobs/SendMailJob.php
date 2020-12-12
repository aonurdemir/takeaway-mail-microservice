<?php

namespace App\Jobs;

use App\Models\Mail;
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
     * @var \App\Models\Mail
     */
    private $mail;

    /**
     * Create a new job instance.
     *
     * @param \App\Models\Mail $mail
     */
    public function __construct(Mail $mail)
    {
        $this->mail = $mail;
    }

    /**
     * Execute the job.
     *
     * @param \App\Services\MailSender $mailManager
     *
     * @return void
     * @throws \App\Exceptions\TypeException
     */
    public function handle(MailSender $mailManager)
    {
        $mailManager->send($this->mail);
    }
}
