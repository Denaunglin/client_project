<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ItemRequest extends FormRequest
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
            'barcode' => 'required',
            'name' => 'required',
            'unit' => 'required',
            'item_category_id' => 'required',
            'item_sub_category_id' => 'required',
            'buying_price' => 'required',
            'retail_price' => 'required',
            'wholesale_price' => 'required',
            'expire_status' => 'required',

        ];
    }

    public function messages()
    {
        return [
            'barcode.required' => 'Item Barcode field is required.',
            'name.required' => 'Item Name field is required.',
            'unit.required' => 'Item Unit field is required.',
            'item_category_id.required' => 'Item Category field is required',
            'item_sub_category_id.required' => 'Item Sub Category field is required',
            'buying_price.required' => 'Item Buying Price is required',
            'retail_price.required' => 'Item Retail Price is required',
            'wholesale_price.required' => 'Item WholeSale Pirce is required',
            'expire_status.required' => 'Item Expire Status is required',
        ];
    }
}
