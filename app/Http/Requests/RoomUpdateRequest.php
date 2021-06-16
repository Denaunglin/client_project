<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RoomUpdateRequest extends FormRequest
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
            'room_type' => 'required',
            'bed_type' => 'required',
            'adult_qty' => 'required',
            'price' => 'required',
            'foreign_price' => 'required',
            'description' => 'required',
            'room_qty' => 'required',

            'facilities' => 'required',
        ];
    }
    public function messages()
    {
        return [
            'room_type.required' => 'Room Type field is required !',
            'bed_type.required' => 'Bed Type field is required !',
            'adult_qty.required' => 'Adult Qty field is required !',
            'price.required' => 'Price field is required !',
            'foreign_price.required' => 'Foreign price field is required !',
            'description.required' => 'Description field is required !',
            'room_qty.required' => 'Room Qty field is required !',

            'facilities.required' => 'facilities field is required !',
        ];
    }
}
