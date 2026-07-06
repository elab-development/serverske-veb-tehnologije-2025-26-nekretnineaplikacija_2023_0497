<?php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PropertyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'title'       => $this->title,
            'description' => $this->description,
            'price'       => $this->price,
            'location'    => $this->location,
            'type'        => $this->type,
            'bedrooms'    => $this->bedrooms,
            'bathrooms'   => $this->bathrooms,
            'area_sqm'    => $this->area_sqm,
            'status'      => $this->status,
            'owner'       => [
                'id'   => $this->user->id,
                'name' => $this->user->name,
            ],
            'features'    => $this->features,
            'created_at'  => $this->created_at->format('d.m.Y'),
        ];
    }
}