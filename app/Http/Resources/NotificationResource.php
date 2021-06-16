<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class NotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        $read_at = $this->read_at ? $this->read_at : '';
        $read_at_date = $read_at ? Carbon::parse($read_at) : '';
        $read_at_ago = $read_at ? $read_at->diffForHumans() : '';
        return [
            'id' => $this->id,
            'title' => $this->data['title'] ?? '-',
            'detail' => Str::limit($this->data['detail'], 100),
            'date' => Carbon::parse($this->created_at)->format('Y-m-d H:i:s'),
            'ago' => $this->created_at->diffForHumans(),
            'read_at_date' => $read_at_date,
            'read_at_ago' => $read_at_ago,
            'web_link' => $this->data['web_link'],
            'deep_link' => $this->data['deep_link'],
        ];
    }
}
