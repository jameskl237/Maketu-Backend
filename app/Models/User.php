<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
     protected $fillable = [
        'name',
        'username',
        'email',
        'role',
        'phone',
        'address',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // 🏪 Un utilisateur peut avoir plusieurs boutiques
    public function shops()
    {
        return $this->hasMany(Shop::class);
    }

    // 📦 Un utilisateur peut avoir plusieurs produits (qu’il a créés)
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
