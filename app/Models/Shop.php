<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'city',
        'district',
        'user_id',
    ];

    // Une boutique appartient à un utilisateur (supplier)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Une boutique a plusieurs produits
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
