<?php

namespace App\Http\Requests;

use App\Rules\Captcha;
use Illuminate\Foundation\Http\FormRequest;

class StoreBooking extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'nationality' => 'required|integer|between:1,2',
            'name' => 'required',
            'email' => 'required|email',
            'phone' => 'required|numeric',
            'nrc_passport' => 'required',
            'term' => 'required',
            'room_qty' => 'required',
            'guest' => 'required',
            'pay_method' => 'required',
            'check_in' => 'required',
            'check_out' => 'required',
            'g-recaptcha-response' => new Captcha(),
        ];
    }

    public function messages()
    {
        return [
            'nationality.required' => 'Please choose nationality back !',
            'nationality.between' => 'nationality must be binary',
            'name.required' => 'Name field is required.',
            'email.required' => 'Email field is required.',
            'phone.required' => 'Phone field is required.',
            'term.required' => 'Please Check Term & Conditions',
            'pay_method.required' => 'Pay method field is required',
            'room_qty.required' => 'Room Qty field is required.',
            'guest.required' => 'Guest field is required.',
            'check_in.required' => 'Check in field is required.',
            'check_out.required' => 'Check out field is required.',
        ];
    }
}
