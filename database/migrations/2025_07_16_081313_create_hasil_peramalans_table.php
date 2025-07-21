<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHasilPeramalansTable extends Migration
{
    public function up()
    {
        Schema::create('hasil_peramalans', function (Blueprint $table) {
            $table->id();
            $table->string('kategori');
            $table->string('jenis_barang');
            $table->year('tahun');
            $table->double('aktual');
            $table->double('hasil');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('hasil_peramalans');
    }
}
