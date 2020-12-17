<?php

namespace App\Console\Commands;

use App\Services\MailService;
use Illuminate\Console\Command;
use Illuminate\Validation\ValidationException;

class SendEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:send 
                            {from : Sender address}
                            {to : Recipient address}
                            {--subject= : Mail subject}
                            {--content= : Mail content}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Email sending command';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle(MailService $mailService): int
    {
        $attributes = array_merge($this->arguments(), $this->options());

        try {
            $mailService->create($attributes);
            $this->info('Successful');
        } catch (ValidationException $e) {
            $this->error($e->getMessage());
        }

        return 0;
    }
}
