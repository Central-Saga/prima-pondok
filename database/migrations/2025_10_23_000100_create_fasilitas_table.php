<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fasilitas', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 150);
            $table->string('nama_en', 150)->nullable();
            $table->timestamps();
        });

        Schema::create('fasilitas_kamar', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fasilitas_id')->constrained('fasilitas')->cascadeOnDelete();
            $table->foreignId('kamar_id')->constrained('kamar')->cascadeOnDelete();
            $table->unique(['fasilitas_id', 'kamar_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fasilitas_kamar');
        Schema::dropIfExists('fasilitas');
    }
};

