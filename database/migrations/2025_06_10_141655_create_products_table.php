<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('kode_product')->nullable();
            $table->string('nama_product');
            $table->string('stock_product')->nullable();
            $table->string('harga_jual')->nullable();
            $table->string('harga_pokok')->nullable(); // uptodate setiap kali restock
            $table->string('barcode');
            $table->enum('satuan', [
                '0', //pcs
                '1', //pack
                '2', //karton
                '3', //gr/kg
                '4', //l/ml
            ])->default('0');
            $table->string('jenis')->nullable();
            $table->string('merek')->nullable();
            $table->foreignId('fk_id_toko')->constrained('tokos')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('products');
    }
}
