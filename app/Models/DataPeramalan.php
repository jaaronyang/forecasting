<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataPeramalan extends Model
{
    use HasFactory;

    protected $fillable = [
    'kategori',
    'jenis_barang',
    'tahun',
    'hasil_peramalan',
];

}
