<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransaksisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaksis', function (Blueprint $table) {
            $table->id();
            $table->string('no_invoice');
            $table->integer('total_bayar');
            $table->integer('total_modal');
            $table->enum('jenis_transaksi', [
                '0', // transaksi dengan pembeli
                '1' // transaksi konsumsi kasir
            ]);
            $table->foreignId('fk_id_kasir')->constrained('users')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('transaksis');
    }
}
