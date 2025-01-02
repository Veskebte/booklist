<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $fillable = [
        'isbn',
        'judul',
        'penulis',
        'penerbit',
        'genre',
        'deskripsi',
        'foto',
    ];

    public function getFotoAttribute($value) {
        return $value ? asset('storage/'.$value) : null;
    }

    public function favorites() {
        return $this->hasMany(Favorite::class);
    }

    public function likeFavorite() {
        return $this->favorites()->count();
    }
}
