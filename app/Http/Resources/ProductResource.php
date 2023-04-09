<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
          return
              [
                  'name' => $this->name,
                  'brand_id' => $this->brand_id,
                  'category_id' => $this->category_id,
                  'image' => 'storage/app/image/products/'.$this->image,
                  'description' => $this->description,
                  'price' => $this->price,
                  'delivery_amount' => $this->delivery_amount,
                  'quantity' => $this->quantity,
                  'gallery' => Product_imageResource::collection($this->whenLoaded('images'))
              ];
    }
}
