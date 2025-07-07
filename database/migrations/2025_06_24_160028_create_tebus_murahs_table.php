<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTebusMurahsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tebus_murahs', function (Blueprint $table) {
            $table->id();
            $table->string('harga');
            $table->string('minimal_belanja');
            $table->date('start');
            $table->date('end');
            $table->integer('stock');
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
        Schema::dropIfExists('tebus_murahs');
    }
}
