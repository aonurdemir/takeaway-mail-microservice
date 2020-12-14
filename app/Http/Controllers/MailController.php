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
        $mail = new MailJob(
            [
                'from'    => $request->input('from'),
                'to'      => $request->input('to'),
                'subject' => $request->input('subject'),
                'content' => $request->input('content'),
                'state'   => MailJob::STATE_CREATED,
            ]
        );
        $mail->save();
        SendMailJob::dispatch($mail);

        return response()->json(['message' => 'ok'], 202);
    }
}
