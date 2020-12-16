<?php

namespace App\Http\Controllers;

use App\Actions\MailService;
use Illuminate\Http\Request;

class MailController extends Controller
{
    /**
     * @var \App\Actions\MailService
     */
    private MailService $mailService;

    public function __construct(MailService $mailService)
    {
        $this->mailService = $mailService;
    }

    /**
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $requestPayload = $request->only(
            [
                'from',
                'to',
                'subject',
                'content',
            ]
        );

        $this->mailService->create($requestPayload);

        return response()->json(['message' => 'ok'], 202);
    }
}
