<?php

namespace App\Repositories;

use App\Models\Mail;

interface MailRepository
{
    public function create($attributes): Mail;
}