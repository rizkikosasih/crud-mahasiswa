<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    Schema::create('mahasiswa', function (Blueprint $table) {
      $table->id();
      $table->string('nama')->default('');
      $table->string('alamat')->default('');
      $table->enum('jenis_kelamin', ['pria', 'wanita'])->default('pria');
      $table->text('file_krs')->default('');
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('mahasiswa');
  }
};
