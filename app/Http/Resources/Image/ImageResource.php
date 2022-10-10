<?php

namespace App\Http\Resources\Image;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ImageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        // $this-path = images/1e23422e393d1e6899390a46341b90fb.jpg
        return [
            'id' => $this->id,
            'url' => $this->url,
            'preview_url' => $this->preview_url,
            'size' => Storage::disk('public')->size($this->path),
            'name' => str_replace('images/', '', $this->path),
        ];
    }
}
