<?php

namespace App\Http\Controllers;

use App\Actions\MailJobService;
use Illuminate\Http\Request;

class MailController extends Controller
{
    /**
     * @var \App\Actions\MailJobService
     */
    private MailJobService $mailJobService;

    public function __construct(MailJobService $mailJobService)
    {
        $this->mailJobService = $mailJobService;
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

        $this->mailJobService->create($requestPayload);

        return response()->json(['message' => 'ok'], 202);
    }
}
