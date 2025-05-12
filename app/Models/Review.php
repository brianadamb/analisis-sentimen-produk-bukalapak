<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'title',
        'konten',
        'komen_name',
        'rate',
        'clean',
        'casefolding',
        'normalization',
        'tokenizing',
        'stopword',
        'stemming',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}