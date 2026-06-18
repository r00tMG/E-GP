<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MealsResource extends JsonResource
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
            'user' => new UserResource(
                $this->resource->user
            ),
            'recipe' => new RecipeResource(
                $this->resource->recipe
            ),
            'meal_type' => $this->resource->meal_type,
            'date' => $this->resource->date
        ];
    }
}
