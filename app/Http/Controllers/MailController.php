<?php

namespace App\Http\Controllers;

use App\Jobs\SendMailJob;
use App\Models\MailJob;
use Illuminate\Http\Request;

class MailController extends Controller
{
    /**
     * Create a mail job
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $mailJob = new MailJob(
            [
                'from'    => $request->input('from'),
                'to'      => $request->input('to'),
                'subject' => $request->input('subject'),
                'content' => $request->input('content'),
                'state'   => MailJob::STATE_CREATED,
            ]
        );
        $mailJob->save();
        SendMailJob::dispatch($mailJob);

        return response()->json(['message' => 'ok'], 202);
    }
}
