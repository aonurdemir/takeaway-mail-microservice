<?php

namespace App\Http\Controllers;


use App\Events\CustomerRegistered;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function store(Request $request)
    {
        $attributes = $request->only(
            [
                'email',
            ]
        );

        //Assume a user service is creating the new user and dispatches CustomerRegistered event
        CustomerRegistered::dispatch();

        return response()->json(['message' => 'created'], 201);
    }
}
