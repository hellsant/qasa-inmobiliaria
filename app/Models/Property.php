<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Property extends Model
{
    protected $fillable = [
        'title', 'slug', 'description', 'operation', 'type', 'zone_id',
        'price', 'currency', 'price_suffix', 'bedrooms', 'bathrooms',
        'area_m2', 'parking', 'address', 'lat', 'lng', 'features',
        'video_url', 'social_tiktok', 'social_instagram', 'social_facebook', 'social_youtube', 'is_featured', 'is_active', 'status',
    ];

    protected $casts = [
        'features'     => 'array',
        'is_featured'  => 'boolean',
        'is_active'    => 'boolean',
        'price'        => 'float',
        'area_m2'      => 'float',
    ];

    public const OPERATIONS = ['venta' => 'Venta', 'alquiler' => 'Alquiler', 'anticretico' => 'Anticrético'];
    public const TYPES = [
        'casa' => 'Casa', 'departamento' => 'Departamento', 'penthouse' => 'Penthouse',
        'garzonier' => 'Garzonier', 'condominio' => 'Condominio', 'terreno' => 'Terreno',
    ];
    public const FALLBACK_IMG = 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=1200&q=60';

    public static function booted(): void
    {
        static::creating(function (Property $p) {
            if (empty($p->slug)) {
                $p->slug = Str::slug($p->title) . '-' . Str::lower(Str::random(4));
            }
        });
    }

    public function zone()     { return $this->belongsTo(Zone::class); }
    public function images()   { return $this->hasMany(PropertyImage::class)->orderBy('sort'); }

    public function scopeActive($q)   { return $q->where('is_active', true); }
    public function scopeFeatured($q) { return $q->where('is_featured', true); }

    public function cover(): ?PropertyImage
    {
        return $this->images->firstWhere('is_cover', true) ?? $this->images->first();
    }

    public function getCoverUrlAttribute(): string
    {
        return $this->cover()?->url ?? self::FALLBACK_IMG;
    }

    public function getPriceLabelAttribute(): string
    {
        $symbol = strtoupper($this->currency) === 'USD' ? '$us' : 'Bs';
        $label  = $symbol . ' ' . number_format($this->price, 0, ',', '.');
        return $this->price_suffix ? $label . ' ' . $this->price_suffix : $label;
    }

    public function getOperationLabelAttribute(): string { return self::OPERATIONS[$this->operation] ?? $this->operation; }
    public function getTypeLabelAttribute(): string      { return self::TYPES[$this->type] ?? $this->type; }
}