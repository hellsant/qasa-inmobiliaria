<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyImage extends Model
{
    protected $fillable = ['property_id', 'path', 'is_cover', 'sort'];

    protected $casts = ['is_cover' => 'boolean'];

    public function property() { return $this->belongsTo(Property::class); }

    /** Soporta imágenes subidas y URLs externas (seeder). */
    public function getUrlAttribute(): string
    {
        return str_starts_with($this->path, 'http') ? $this->path : asset('storage/' . $this->path);
    }
}