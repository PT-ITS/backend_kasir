<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductPromosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_promos', function (Blueprint $table) {
            $table->id();
            $table->integer('harga');
            $table->integer('stock');
            $table->foreignId('fk_id_product')->constrained('products')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('fk_id_promo')->constrained('promos')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('product_promos');
    }
}
