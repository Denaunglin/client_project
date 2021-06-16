<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProfileCreditCardResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'card_id' => $this->id,
            'card_type_id' => $this->cardtype ? $this->cardtype->id : '-',
            'card_type' => $this->cardtype ? $this->cardtype->name : '-',
            'account_name' => $this->account_name,
            'card_no' => $this->credit_no,
            'expire_month' => $this->expire_month,
            'expire_year' => $this->expire_year,
            'expire' => $this->expire_month . '/' . $this->expire_year,

        ];
    }
}
