<?php

namespace App\Repositories;

use App\Models\Mail;

interface MailJobRepository
{
    public function create($attributes): Mail;
}