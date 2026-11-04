<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Produk extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'produk';

    protected $fillable = [
        'nama',
        'kategori',
        'harga',
        'stok'
    ];
}