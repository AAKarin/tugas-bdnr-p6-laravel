<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Buku extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'buku';

    protected $fillable = [
        'judul',
        'pengarang',
        'kategori',
        'tahun',
        'stok',
        'peminjam',
        'ulasan'
    ];

    protected $attributes = [
        'peminjam' => [],
        'ulasan' => []
    ];
}
