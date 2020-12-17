<?php

namespace App\Repositories;

use App\Models\Mail;

class EloquentMailRepository implements MailRepository
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