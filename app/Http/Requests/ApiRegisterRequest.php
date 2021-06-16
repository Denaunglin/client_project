<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApiRegisterRequest extends FormRequest
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
            'phone' => 'required|unique:users|numeric|min:8',
            'nrc_passport' => 'required',
            'front_pic' => 'required',
            'back_pic' => 'required',
            'date_of_birth' => 'required',
            'gender' => 'required',
            'address' => 'required',
            'password' => 'required|confirmed|string|min:8',
            'password_confirmation' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'nrc_passport.required' => 'The NRC or Passport field is required.',
            'front_pic.required' => 'The front picture of NRC or Passport field is required.',
            'back_pic.required' => 'Back Picture of Nrc or Passport field is required.',
        ];
    }
}
