<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class ListingImage extends Model
{
    use HasUuids, HasFactory;

    protected $fillable = [
        'listing_id',
        'image_url',
        'sort_order',
    ];

    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }
}
