<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    protected $fillable = [
        'kind', 'name', 'phone', 'interest', 'zone',
        'property_type', 'operation', 'area_m2', 'message', 'is_read',
    ];

    protected $casts = ['is_read' => 'boolean'];

    public function getKindLabelAttribute(): string
    {
        return $this->kind === 'tasacion' ? 'Tasación' : 'Contacto';
    }
}