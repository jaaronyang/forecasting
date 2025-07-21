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
    Schema::create('pengujian', function (Blueprint $table) {
        $table->id();
        $table->string('kategori');
        $table->string('jenis_barang');
        $table->text('tahun'); // biasanya disimpan dalam bentuk JSON string
        $table->double('mse');
        $table->double('mape');
        $table->longText('data_json')->nullable(); // simpan detail pengujian
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
        Schema::dropIfExists('pengujian');
    }
};
