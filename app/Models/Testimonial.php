<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    protected $fillable = ['quote', 'author', 'operation', 'location', 'photo', 'sort'];

    public function getPhotoUrlAttribute(): string
    {
        if (!$this->photo) return 'https://ui-avatars.com/api/?background=c8a24a&color=fff&name=' . urlencode($this->author);
        return str_starts_with($this->photo, 'http') ? $this->photo : asset('storage/' . $this->photo);
    }
}