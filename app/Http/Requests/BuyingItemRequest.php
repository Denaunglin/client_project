<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BuyingItemRequest extends FormRequest
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
            'item_id' => 'required',
            'qty' => 'required',
            'price' => 'required',
            'net_price' => 'required',
        ];
    }
}
