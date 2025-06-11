<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBiayaOperasionalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('biaya_operasionals', function (Blueprint $table) {
            $table->id();
            $table->string('nama_operasional');
            $table->string('waktu_operasional');
            $table->string('jumlah_biaya');
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
        Schema::dropIfExists('biaya_operasionals');
    }
}
