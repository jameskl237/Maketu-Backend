<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable =[
        'name',
        'description',
        'long_description',
        'price',
        'promotion_price',
        'in_stock',
        'origin',
        'quantity',
        'category_id',
        'user_id',
        'shop_id',
        'code'
    ];

    protected $casts = [
        'in_stock' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function medias(): HasMany
    {
        return $this->hasMany(Media::class);
    }

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class, 'shop_id');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}
