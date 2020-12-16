<?php

namespace App\Repositories;

use App\Models\Mail;

class EloquentMailJobRepository implements MailJobRepository
{
    public function create($attributes): Mail
    {
        $attributes = $this->addCreatedStateToAttributes($attributes);

        return Mail::create($attributes);
    }

    private function addCreatedStateToAttributes($attributes)
    {
        $attributes['state'] = Mail::STATE_CREATED;

        return $attributes;
    }
}