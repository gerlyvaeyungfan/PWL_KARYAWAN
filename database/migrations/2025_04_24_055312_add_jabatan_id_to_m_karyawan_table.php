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
        Schema::create('m_karyawan', function (Blueprint $table) {
            $table->id('karyawan_id'); // Primary key
            $table->string('nama');
            $table->unsignedBigInteger('jabatan_id'); // Jabatan_id tidak nullable
            $table->text('alamat');
            $table->string('telepon');
            $table->string('email')->nullable();
            $table->timestamps(); // created_at & updated_at

            // Mendefinisikan Foreign Key pada kolom jabatan_id yang mengacu pada kolom jabatan_id di tabel m_jabatan
            $table->foreign('jabatan_id')->references('jabatan_id')->on('m_jabatan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_karyawan');
    }
};
