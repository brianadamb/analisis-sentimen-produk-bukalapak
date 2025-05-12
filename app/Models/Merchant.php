<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Merchant extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_toko',
        'nama_toko',
        'link'
    ];

    public function product()
    {
        return $this->hasMany(Product::class);
    }
}