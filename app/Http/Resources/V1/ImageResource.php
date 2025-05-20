<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ImageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'filename' => $this->name,
            'original_path' => \Illuminate\Support\Facades\URL::to($this->path),
            'output_path' => \Illuminate\Support\Facades\URL::to($this->output_path),
            'album_id' => $this->album_id,
            'created_at' => $this->created_at
        ];
    }
}
