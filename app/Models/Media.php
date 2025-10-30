<?php

namespace App\Models;

use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Media extends Model
{
    protected $table = 'medias'; 
     protected $appends = ['url'];

    public function getUrlAttribute()
    {
        // Si le fichier existe, retourne une URL complète
        return $this->file_path
            ? Storage::url($this->file_path)
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
