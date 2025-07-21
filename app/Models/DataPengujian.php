<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataPengujian extends Model
{
    protected $table = 'pengujian'; // atau 'data_pengujian' jika sesuai migration
    protected $fillable = ['kategori', 'jenis_barang', 'tahun', 'mse', 'mape', 'data_json'];
    public function peramalan()
    {
        return $this->belongsTo(DataPeramalan::class, 'data_peramalan_id');
    }
}
