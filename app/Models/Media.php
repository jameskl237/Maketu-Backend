<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Media extends Model
{
    use HasFactory;
    
    protected $table = 'medias'; 
     protected $appends = ['full_url'];

    public function getFullUrlAttribute()
    {
        // Si le fichier existe, retourne une URL complète
        return $this->url
            ? asset(Storage::url($this->url))
            : null;
    }
    
    protected $fillable = [
        'url',
        'type',
        'is_principal',
        'product_id'
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
