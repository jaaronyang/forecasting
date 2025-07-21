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
    Schema::create('produksis', function (Blueprint $table) {
        $table->id();
        $table->string('kategori'); // tambang / jaring / benang
        $table->string('bulan');
        $table->year('tahun');
        $table->integer('jumlah_produksi');
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
        Schema::dropIfExists('produksis');
    }
};
