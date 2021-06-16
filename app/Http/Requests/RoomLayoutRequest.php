<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RoomLayoutRequest extends FormRequest
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
            'room_no' => 'required',
            'floor' => 'required',
            'rank' => 'required|unique:room_layouts',
        ];
    }
    public function messages()
    {
        return [
            'room_id.required' => 'Room Id field is required !',
            'room_no.required' => 'Room No field is required !',
            'floor.required' => 'Please choose the floor field !',
            'rank.required' => 'Please choose the rank field !',
            'rank.unique' => 'The Rank you choose is already exist',
        ];
    }
}
