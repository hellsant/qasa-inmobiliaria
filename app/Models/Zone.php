<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Zone extends Model
{
    protected $fillable = ['name', 'group', 'price_m2', 'active'];

    public const GROUPS = [
        'centro' => 'Centro & Prado',
        'norte'  => 'Norte',
        'oeste'  => 'Oeste & América',
        'sur'    => 'Sur',
        'valle'  => 'Valle',
    ];

    public function properties()
    {
        return $this->hasMany(Property::class);
    }

    public function getGroupLabelAttribute(): string
    {
        return self::GROUPS[$this->group] ?? ucfirst($this->group);
    }
}