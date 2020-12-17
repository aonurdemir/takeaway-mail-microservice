<?php


namespace App\Services;


use App\Jobs\SendMailJob;
use App\Repositories\MailRepository;
use Illuminate\Support\Facades\Validator;

class MailService
{
    private MailRepository $mailRepository;

    public function __construct(MailRepository $mailRepository)
    {
        $this->mailRepository = $mailRepository;
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

        $mailJob = $this->mailRepository->create($attributes);
        SendMailJob::dispatchAfterResponse($mailJob);

        return $mailJob;
    }

}