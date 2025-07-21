<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('bahan_bakus', function (Blueprint $table) {
    $table->id();
    $table->string('kategori'); // tambang / jaring / benang
    $table->string('bulan');
    $table->year('tahun');
    $table->integer('jumlah_bahanbaku');
    $table->timestamps();
});

    }

    public function down() {
        Schema::dropIfExists('bahan_bakus');
    }
};
