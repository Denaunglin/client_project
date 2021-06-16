<?php

namespace App\Http\Resources;

use App\Http\Resources\CardTypeResource;
use App\Http\Resources\ProfileCreditCardResource;
use App\Models\CardType;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileEditResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $front_pic = $this->usernrcimage->image_path_front();
        $back_pic = $this->usernrcimage->image_path_back();
        $profile_image = $this->userprofile ? $this->userprofile->image_path() : null;

        $credit_card = $this->usercreditcard ? $this->usercreditcard : null;
        $credit_resource = ProfileCreditCardResource::collection($credit_card);

        $credit_cards_type = CardType::where('trash', 0)->get();
        $card_type = CardTypeResource::collection($credit_cards_type);

        return [
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'nrc_passport' => $this->nrc_passport,
            'front_pic' => $front_pic,
            'back_pic' => $back_pic,
            'date_of_birth' => $this->date_of_birth,
            'gender' => $this->gender,
            'credit_card' => $credit_resource,
            'card_type' => $card_type,
            'profile_image' => $profile_image,
            'address' => $this->address,

        ];
    }
}
