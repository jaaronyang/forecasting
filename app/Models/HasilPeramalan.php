<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HasilPeramalan extends Model
{
    use HasFactory;

    protected $table = 'hasil_peramalans';

    protected $fillable = [
    'kategori',
    'jenis_barang',
    'tahun',
    'bulan',
    'aktual',
    'hasil',
];
}
