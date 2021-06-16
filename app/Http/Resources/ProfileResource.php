<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $profile_image = $this->userprofile ? $this->userprofile->image_path() : null;
        $user_accounttype = ["id" => $this->accounttype ? $this->accounttype->id : null,
            "name" => $this->accounttype ? $this->accounttype->name : null,
            "room_limit" => $this->accounttype ? $this->accounttype->booking_limit : 0];

        if ($this->gender == 'male') {
            $gender = 'Male';
        } else if ($this->gender == 'female') {
            $gender = 'Female';
        } else {
            $gender = null;
        }

        $credit_card = $this->usercreditcard ? $this->usercreditcard : null;
        $credit_resource = ProfileCreditCardResource::collection($credit_card);

        return [
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'nrc_passport' => $this->nrc_passport,
            'date_of_birth' => $this->date_of_birth,
            'gender' => $gender,
            'gender_no' => $this->gender,
            'address' => $this->address,
            'profile_image' => $profile_image,
            'user_accounttype' => $user_accounttype,
            'credit_resource' => $credit_resource,
        ];

    }
}
