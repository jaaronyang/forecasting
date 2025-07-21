<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
   public function up()
{
    Schema::create('data_peramalans', function (Blueprint $table) {
        $table->id();
        $table->string('kategori'); // produksi / bahanbaku
        $table->string('jenis_barang'); // tambang / jaring / benang
        $table->json('tahun'); // array tahun, disimpan dalam format JSON
        $table->json('hasil_peramalan'); // semua hasil disimpan dalam bentuk JSON
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('data_peramalans');
    }
};
