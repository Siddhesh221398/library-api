<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Book extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'author', 'description', 'status'];

    public function borrows()
    {
        return $this->hasMany(Borrow::class);
    }

    protected static function booted()
    {
        static::saved(fn() => Cache::forget('books_list'));
        static::deleted(fn() => Cache::forget('books_list'));
    }
}
