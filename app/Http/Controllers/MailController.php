<?php

namespace App\Http\Controllers;

use App\Jobs\SendMailJob;
use App\Models\Mail;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

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
        $mail = new Mail(
            [
                'from'    => $request->input('from'),
                'to'      => $request->input('to'),
                'subject' => $request->input('subject'),
                'content' => $request->input('content'),
                'state'   => Mail::STATE_CREATED,
            ]
        );
        $mail->save();
        SendMailJob::dispatch($mail);

        return response()->json(['message' => 'ok'], 202);
    }
}
