<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DiscountsRequest extends FormRequest
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
            'user_account_id' => 'required',
            'room_type_id' => 'required',
            'discount_percentage_mm' => 'required',
            'discount_percentage_foreign' => 'required',
            'discount_amount_mm' => 'required',
            'discount_amount_foreign' => 'required',
            'addon_percentage_mm' => 'required',
            'addon_percentage_foreign' => 'required',
            'addon_amount_mm' => 'required',
            'addon_amount_foreign' => 'required',

        ];
    }
    public function messages()
    {
        return [
            'user_account_id.required' => 'Please Choose User Type.',
            'room_type_id.required' => 'Please Choose Room Type required.',
            'discount_percentage_mm.required' => 'Discount Percentage MM field is required.',
            'discount_percentage_foreign.required' => 'Discount Percentage Foreign field is required.',
            'discount_amount_mm.required' => 'Discount Amount MM field is required.',
            'discount_amount_foreign.required' => 'Discount Amount Foreign field is required.',
            'addon_percentage_mm.required' => 'addon Percentage MM field is required.',
            'addon_percentage_foreign.required' => 'addon Percentage Foreign field is required.',
            'addon_amount_mm.required' => 'addon Amount MM field is required.',
            'addon_amount_foreign.required' => 'addon Amount Foreign field is required.',
        ];
    }
}
