<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'merchant_id',
        'nama_produk',
        'link'
    ];

    public function merchant()
    {
        return $this->belongsTo(Merchant::class);
    }

    public function review()
    {
        return $this->hasMany(Review::class);
    }
}