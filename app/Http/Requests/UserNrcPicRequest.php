<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserNrcPicRequest extends FormRequest
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
            'user_id' => 'required',
            'front_pic' => 'required',
            'back_pic' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'user_id.required' => 'Please select the user',
            'front_pic.required' => 'User Nrc or Passport Front Image is required ',
            'back_pic.required' => 'User Nrc or Passport Back Image is required '

        ];
    }
}
