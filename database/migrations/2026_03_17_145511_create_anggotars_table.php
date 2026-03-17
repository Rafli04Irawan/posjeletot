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
        Schema::create('anggotars', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('tempat_lahir');
            $table->date('tanggal_lahir');

            $table->string('jenis_kelamin');
            $table->string('agama')->default('Islam');

            $table->string('status_pendidikan'); // diperbaiki dari text → string
            $table->text('alamat');

            $table->string('no_hp');
            $table->string('email');

            $table->string('pendidikan_terakhir');
            $table->string('nama_sekolah');
            $table->string('jurusan')->nullable();
            $table->string('tahun_masuk')->nullable();

            $table->date('tanggal_daftar');

            $table->string('status'); // WAJIB (tadi belum masuk model)
            $table->string('divisi');

            $table->string('foto')->nullable();
            $table->string('kartu_pengenal')->nullable();
            $table->string('formulir')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('anggotars');
    }
};
