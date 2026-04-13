<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'nama',
        'total',
        'diperbaiki',
        'peminjaman',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function lendings()
    {
        return $this->hasMany(Lending::class);
    }
}
