<?php

namespace App\Http\Requests\Api;

use Dingo\Api\Http\FormRequest;

class CaptchaRequest extends FormRequest
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

    public function rules()
    {
        return [
            'phone' => 'required|regex:/^1[34578]\d{9}$/|unique:users',
        ];
    }
}
