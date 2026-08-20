<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeamMember extends Model
{
    protected $fillable = ['name', 'role', 'photo', 'sort'];

    public function getPhotoUrlAttribute(): string
    {
        if (!$this->photo) return 'https://ui-avatars.com/api/?background=12211a&color=e0c176&name=' . urlencode($this->name);
        return str_starts_with($this->photo, 'http') ? $this->photo : asset('storage/' . $this->photo);
    }
}