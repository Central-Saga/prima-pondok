<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('pemesanan_id')->unique();
            $table->unsignedBigInteger('wisatawan_id');
            $table->unsignedBigInteger('kamar_id');

            $table->unsignedTinyInteger('rating');
            $table->text('komentar');
            $table->boolean('is_published')->default(false);

            $table->timestamps();

            $table->foreign('pemesanan_id')->references('id')->on('pemesanan')->cascadeOnDelete();
            $table->foreign('wisatawan_id')->references('id')->on('wisatawan')->cascadeOnDelete();
            $table->foreign('kamar_id')->references('id')->on('kamar')->cascadeOnDelete();

            $table->index(['is_published', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};

