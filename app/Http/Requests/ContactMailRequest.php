<?php

namespace App\Http\Requests;

use App\Rules\Captcha;
use Illuminate\Foundation\Http\FormRequest;

class ContactMailRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required',
            'email' => 'required|email',
            'phone' => 'required|numeric',
            'message' => 'required',
            'g-recaptcha-response' => new Captcha(),
            'my_name' => 'honeypot',
            'my_time' => 'required|honeytime:5',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'The name field is required.',
            'email.required' => 'The email field is required.',
            'phone.required' => 'The phone field is required.',
            'message.required' => 'The message field is required.',
        ];
    }
}
