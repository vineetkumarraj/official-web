<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\SignupRequest;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class UserAuth extends Controller
{
    public function login(LoginRequest $request) 
    {
        $user = User::where('email', $request->username)->orWhere('mobile', $request->username)->first();

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

    public function social_login(Request $request)
    {
        try {
            if ($request->social_platform == 'google') {
                $accessTokenResponse = Socialite::driver('google')->getAccessTokenResponse($request->token);
                $accessToken = $accessTokenResponse["access_token"];
                $get_user = Socialite::driver('google')->userFromToken($accessToken);
            } else {
                $get_user = Socialite::driver($request->platform)->userFromToken($request->token);
            }
        } catch (Exception $exception) {
            return $this->unprocessable_request($exception->getMessage());
        }

        $check_email = User::where(['email' => $get_user->email]);

        if ($check_email->exists()) {
            // update or create is used cause we need to get the collection wich is being updated, this won't craete new row as we already checked the email
            $user = User::where('email', $get_user->email)->updateOrCreate([
                'device_token' => $request->device_token,
                'device' => $request->device,
                'fcm_token' => $request->fcm_token,
                'lat' => $request->lat,
                'long' => $request->long,
            ]);
        } else {

            // We are created random 4 digit pin and 10 digit pin for those who comes for the first time
            $pin = rand(1000, 9999);
            $username = time();
            $username = User::where(['username' => $username]);

            //double check if the username is existed, if yes then create it again
            $username = $username->exists() ? time() + rand(10000000, 99999999) : $username;
            $user = new User([
                'email' => $get_user->email,
                'username' => $username,
                'pin' => $pin,
                'device_token' => $request->device_token,
                'device' => $request->device,
                'profilepic' => $get_user->avatar,
                'displayname' => $get_user->user['name'],
                'firstname' => $get_user->user['given_name'],
                'lastname' => $get_user->user['family_name'],
                'fcm_token' => $request->fcm_token,
                'lat' => $request->lat,
                'long' => $request->long,
            ]);

            $user->save();
        }

        $tokenResult = $user->createToken('login instantly after getting registered');
        $token = $tokenResult->token;

        $token->save();

        $user['access_token'] = $tokenResult->accessToken;
        $user['expires_at'] = Carbon::parse(
            $tokenResult->token->expires_at
        )->toDateTimeString();

        $data = array(
            'message' => 'you have been logged in with. ' . $request->platform,
            'data' => $user
        );

        return response()->json($data);
    }

    public function signup(SignupRequest $request){
        return 0;
    }
    
    public function logout()
    {
        return 0;
    }
}