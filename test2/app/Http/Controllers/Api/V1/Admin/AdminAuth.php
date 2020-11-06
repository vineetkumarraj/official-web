<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminAuth extends Controller
{
    public function login(LoginRequest $request)
    {
        $user = Admin::where('email', $request->username)->orWhere('mobile', $request->username)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->unprocessable_request('these credentials do not match our records');
        }

        $token = $user->createToken('User Login')->plainTextToken;
        $user->token = $token;

        $response = [
            'message' => 'Logged in!',
            'data' => $user
        ];

        return response($response);
    }

    public function logout()
    {
        return 0;
    }
}
