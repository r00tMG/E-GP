<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RecipeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->resource->id,
            'title' => $this->resource->title,
            'image' => $this->resource->image,
            'summary' => $this->resource->summary,
            'instructions' => $this->resource->instructions,
            'nutrition' => $this->resource->nutrition,
            'video' => $this->resource->video,
            'prepTime' => $this->resource->prepTime,
            'cookTime' => $this->resource->cookTime,
        ];
    }
}
