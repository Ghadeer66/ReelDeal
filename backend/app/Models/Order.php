<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Order extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'buyer_id',
        'seller_id',
        'listing_id',
        'status',
        'amount',
        'currency',
        'stripe_payment_intent_id',
        'stripe_transfer_id',
        'escrow_released_at',
        'delivery_confirmed_at',
        'dispute_opened_at',
        'shipping_address',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'status' => OrderStatus::class,
            'escrow_released_at' => 'datetime',
            'delivery_confirmed_at' => 'datetime',
            'dispute_opened_at' => 'datetime',
            'shipping_address' => 'array',
        ];
    }

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }

    public function review()
    {
        return $this->hasOne(Review::class);
    }
}
