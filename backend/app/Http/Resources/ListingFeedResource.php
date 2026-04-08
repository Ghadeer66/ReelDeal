<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ListingFeedResource extends JsonResource
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
            'title_en' => $this->title_en,
            'title_ar' => $this->title_ar,
            'description_en' => $this->description_en,
            'description_ar' => $this->description_ar,
            'price' => $this->price,
            'currency' => $this->currency,
            'condition' => $this->condition,
            'media_type' => $this->media_type,
            'video_url' => $this->video_url,
            'thumbnail_url' => $this->thumbnail_url,
            'status' => $this->status,
            'likes_count' => $this->likes_count,
            'saves_count' => $this->saves_count,
            'views_count' => $this->views_count,
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'avatar_url' => $this->user->avatar_url,
            ],
            'category' => [
                'id' => $this->category->id,
                'name_en' => $this->category->name_en,
                'name_ar' => $this->category->name_ar,
            ],
            'images' => $this->images->map(fn($img) => [
                'url' => $img->image_url,
                'sort' => $img->sort_order
            ]),
            'created_at' => $this->created_at,
        ];
    }
}
