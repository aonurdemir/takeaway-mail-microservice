<?php

namespace App\Repositories;

use App\Models\MailJob;

class EloquentMailJobRepository implements MailJobRepository
{
    public function create($attributes): MailJob
    {
        $attributes = $this->addCreatedStateToAttributes($attributes);

        return MailJob::create($attributes);
    }

    private function addCreatedStateToAttributes($attributes)
    {
        $attributes['state'] = MailJob::STATE_CREATED;

        return $attributes;
    }
}