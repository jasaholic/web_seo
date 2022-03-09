<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProcessKeywordGeneratorRequest extends FormRequest
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
            'keyword' => ['required', 'string', 'min:1', 'max:128'],
            'g-recaptcha-response' => [(config('settings.captcha_keyword_generator') ? 'required' : 'sometimes'), 'captcha']
        ];
    }
}
