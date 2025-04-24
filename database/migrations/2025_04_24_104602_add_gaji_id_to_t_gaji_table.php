<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('t_gaji', function (Blueprint $table) {
            $table->id('transaksi_id'); // auto increment primary key
            $table->unsignedBigInteger('karyawan_id'); // foreign key
            $table->dateTime('tanggal_transaksi'); // datetime field
            $table->decimal('gaji_pokok', 15, 0);
            $table->decimal('tunjangan', 15, 0);
            $table->decimal('potongan', 15, 0);
            $table->decimal('total_gaji', 15, 0)->default(0)->change();
            $table->text('keterangan')->nullable();
            $table->foreign('karyawan_id')->references('karyawan_id')->on('m_karyawan')->onDelete('cascade'); // foreign key constraint
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('t_gaji', function (Blueprint $table) {
            //
        });
    }
};
