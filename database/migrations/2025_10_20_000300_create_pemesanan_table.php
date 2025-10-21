<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pemesanan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wisatawan_id')->constrained('wisatawan')->cascadeOnDelete();
            $table->foreignId('kamar_id')->constrained('kamar')->cascadeOnDelete();
            $table->date('tanggal_checkin');
            $table->date('tanggal_checkout');
            $table->unsignedInteger('jumlah_hari')->default(1);
            $table->decimal('total_bayar', 12, 2)->default(0);
            $table->string('status')->default('pending');
            $table->timestamps();

            $table->index(['kamar_id', 'tanggal_checkin', 'tanggal_checkout'], 'pemesanan_ketersediaan_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pemesanan');
    }
};

