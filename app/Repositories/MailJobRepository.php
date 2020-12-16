<?php

namespace App\Repositories;

use App\Models\MailJob;

interface MailJobRepository
{
    public function create($attributes): MailJob;
}