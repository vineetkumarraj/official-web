<?php

namespace App\Http\Requests;

use App\Http\Requests\BaseRequest;
use App\Rules\Cordinate;
use App\Rules\Username;

class LoginRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'username' => [new Username, 'required|string'],
            'fcm_token' => 'string',
            'password' => 'string|required',
            'device_token' => 'string',
            'device' => 'required|string',
            'lat' => new Cordinate,
            'long' => new Cordinate,
        ];
    }
}
