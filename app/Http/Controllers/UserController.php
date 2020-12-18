<?php

namespace App\Http\Controllers;


use App\Events\CustomerRegistered;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function store(Request $request)
    {
        //Assume a user service is validating input, creating the new user and dispatches CustomerRegistered event

        $request->validate(['email' => 'required|email']);
        $attributes = $request->only(
            [
                'email',
            ]
        );

        CustomerRegistered::dispatch($attributes['email']);

        return response()->json(['message' => 'User Created'], 201);
    }
}
