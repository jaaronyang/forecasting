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
        Schema::create('data_pengujian', function (Blueprint $table) {
    $table->id();
    $table->string('kategori'); // produksi / bahanbaku
    $table->string('jenis_barang'); // tambang, jaring, benang
    $table->json('tahun');
    $table->double('mse');
    $table->double('mape');
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
        Schema::dropIfExists('data_pengujian');
    }
};
