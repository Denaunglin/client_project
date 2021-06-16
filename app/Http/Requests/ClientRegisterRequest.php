<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClientRegisterRequest extends FormRequest
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
            'name' => 'required',
            'email' => 'required|unique:users|email',
            'phone' => 'required|unique:users|numeric',
            'nrc_passport' => 'required',
            'front_pic' => 'required',
            'back_pic' => 'required',
            'date_of_birth' => 'required',
            'gender' => 'required',
            'address' => 'required',
            'password' => 'required|confirmed|string|min:8',
            'password_confirmation' => 'required',
            'term_conditions' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'The name field is required.',
            'email.required' => 'The email field is required.',
            'email.unique' => 'This email address already registered !',
            'phone.required' => 'The phone field is required.',
            'phone.unique' => 'This phone number is already registered !',
            'nrc.required' => 'The Nrc or Passport field is required.',
            'front_pic.required' => 'Front Picture of Nrc or Passport field is required.',
            'back_pic.required' => 'Back Picture of Nrc or Passport field is required.',
            'date_of_birth.required' => 'The Date of Birth field is required.',
            'gender.required' => 'The Gender field is required.',
            'address.required' => 'The Address field is required.',
            'password.required' => 'The Password field is required.',
            'password.min:8' => 'Please use at least 8 password.',
            'password.confirmed' => 'The Confirm password field do not match with password.',
            'term_conditions.required' => 'Please check Terms and Conditions !',
        ];
    }
}
