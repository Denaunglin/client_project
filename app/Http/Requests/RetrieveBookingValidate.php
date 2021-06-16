<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RetrieveBookingValidate extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [

            'booking_no' => 'required',
            'room_type' => 'required',
            'phone' => 'required',
            'checkin_checkout' => 'required',
            // 'g-recaptcha-response' => new Captcha(),
            // 'my_name' => 'honeypot',
            // 'my_time' => 'required|honeytime:5',
        ];
    }

    public function message()
    {
        return [
            'booking_no.required' => 'The booking number field is required !',
            'phone.required' => 'The phone number field is required !',
        ];
    }
}
