<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OtherServiceItem extends FormRequest
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
            'other_services_category_id' => 'required',
            'name' => 'required',
            'charges_mm' => 'required',
            'charges_foreign' => 'required',

        ];
    }
    public function messages()
    {
        return [
            'other_services_category_id.required' => 'Please Select Category !',
            'name.required' => 'Item Name field is required !',
            'charges_mm.required' => 'Charges MM field is required !',
            'charges_foreign.required' => 'Charges Foreign field is required !',

        ];
    }
}
