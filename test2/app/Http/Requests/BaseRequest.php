<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class BaseRequest extends FormRequest
{
    protected function failedValidation(Validator $validator)
    {
        $response = [];
        $response['message'] = trans('Invalid inputs');
        $response['errors'] = $validator->errors();
        throw new HttpResponseException(response()->json($response, 422));
    }
}
