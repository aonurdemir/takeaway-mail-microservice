<?php


namespace App\Actions;


use App\Jobs\SendMailJob;
use App\Models\MailJob;
use Illuminate\Support\Facades\Validator;

class MailJobService
{
    private MailJob $mailJob;

    public function __construct(MailJob $mailJob)
    {
        $this->mailJob = $mailJob;
    }

    /**
     * @param $attributes
     *
     * @return \App\Models\MailJob
     * @throws \Illuminate\Validation\ValidationException
     */
    public function create($attributes)
    {
        $validationRules = [
            'from'    => 'required|email',
            'to'      => 'required|email',
            'subject' => 'nullable|string|max:78',
            //check why the limit is 78 on: https://tools.ietf.org/html/rfc5322#section-2.1.1
            'content' => 'nullable|string',
        ];

        Validator::make(
            $attributes,
            $validationRules
        )->validate();

        $mailJob = $this->mailJob->create($attributes);
        SendMailJob::dispatchAfterResponse($mailJob);

        return $mailJob;
    }

}