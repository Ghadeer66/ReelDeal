<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class ListingView extends Model
{
    use HasUuids;

    protected $fillable = [
        'listing_id',
        'user_id',
        'ip_address',
        'watched_duration',
    ];

    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
