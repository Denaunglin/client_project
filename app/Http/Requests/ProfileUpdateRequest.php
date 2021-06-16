<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileUpdateRequest extends FormRequest
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
            'image' => 'required',
            'name' => 'required',
            'email' => 'required',
            'phone' => 'required',
            'address' => 'required',
            'password' => 'required',
            'password_confirmation' => 'required',
        ];
    }
    public function messages()
    {
        return [
            'image.required' => 'Profile Image is required !',
            'name.required' => 'User Name field is required !',
            'email.required' => 'Email Address field is required !',
            'phone.required' => 'Contact Number field is required !',
            'address.required' => 'Address field is required !',
            'password.required' => 'Password field is required !',
            'password_confirmation.required' => 'Password Confrimation field is required !',
        ];
    }
}
