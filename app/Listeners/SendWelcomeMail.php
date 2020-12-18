<?php

namespace App\Listeners;

use App\Events\CustomerRegistered;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendWelcomeMail implements ShouldQueue
{
    public function __construct()
    {
        //
    }

    public function handle(CustomerRegistered $event)
    {


    }
}
