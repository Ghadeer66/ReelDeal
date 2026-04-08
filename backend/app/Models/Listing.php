<?php

namespace App\Models;

use App\Enums\ListingCondition;
use App\Enums\ListingStatus;
use App\Enums\MediaType;
use App\Enums\PriceFlag;
use App\Enums\SellerType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Builder;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Listing extends Model
{
    use HasUuids, SoftDeletes, HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'title_en',
        'title_ar',
        'description_en',
        'description_ar',
        'price',
        'currency',
        'location',
        'condition',
        'seller_type',
        'status',
        'rejection_reason',
        'media_type',
        'video_url',
        'thumbnail_url',
        'views_count',
        'likes_count',
        'saves_count',
        'market_price_min',
        'market_price_max',
        'price_flag',
    ];

    protected function casts(): array
    {
        return [
            'condition' => ListingCondition::class,
            'seller_type' => SellerType::class,
            'status' => ListingStatus::class,
            'media_type' => MediaType::class,
            'price_flag' => PriceFlag::class,
        ];
    }

    public function scopeLive(Builder $query): void
    {
        $query->where('status', ListingStatus::Live->value);
    }

    public function scopePendingReview(Builder $query): void
    {
        $query->where('status', ListingStatus::PendingReview->value);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function images()
    {
        return $this->hasMany(ListingImage::class)->orderBy('sort_order', 'asc');
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function saves()
    {
        return $this->hasMany(Save::class);
    }

    public function views()
    {
        return $this->hasMany(ListingView::class);
    }
}
