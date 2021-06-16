<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookingCreate extends FormRequest
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
            'room_id' => 'required',
            'client_user' => 'required',
            'guest' => 'required',
            'room_qty' => 'required',
            'extra_bed_qty' => 'required',
            'nationality' => 'required',
            'pay_method' => 'required',
        ];
    }
    public function messages()
    {
        return [
            'room_id.required' => 'Room field is required.',
            'client_user.required' => ' Client User field is required.',
            'guest.required' => 'Guest field is required.',
            'room_qty.required' => 'Room Qty field is required.',
            'extra_bed_qty.required' => 'Extra Bed Qty field is required.',
            'nationality.required' => 'Nationality field is required.',
            'pay_method.required' => 'Pay Method field is required.',
        ];
    }
}
