<?php


namespace App\Actions;


use App\Jobs\SendMailJob;
use App\Repositories\MailJobRepository;
use Illuminate\Support\Facades\Validator;

class MailJobService
{
    private MailJobRepository $mailJobRepository;

    public function __construct(MailJobRepository $mailJobRepository)
    {
        $this->mailJobRepository = $mailJobRepository;
    }

    /**
     * @param $attributes
     *
     * @return \App\Models\Mail
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

        $mailJob = $this->mailJobRepository->create($attributes);
        SendMailJob::dispatchAfterResponse($mailJob);

        return $mailJob;
    }

}