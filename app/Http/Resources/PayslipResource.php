<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PayslipResource extends JsonResource
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
            "success_msg" => 'Successfully uploaded Payslip .',
            'booking_no' => $this->booking_no,
            'payslip_image' => $this->image_path(),
            'remark' => $this->remark,
            'created_at' => $this->created_at->format('Y-m-d h:i:m a'),
        ];
    }
}
