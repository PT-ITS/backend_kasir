<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTambahStocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tambah_stocks', function (Blueprint $table) {
            $table->id();
            $table->integer('jumlah');
            $table->integer('harga_beli');
            $table->date('expired');
            $table->foreignId('fk_id_catatan_stock')->constrained('catatan_stocks')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('fk_id_product')->constrained('products')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('tambah_stocks');
    }
}
