<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pemesanan', function (Blueprint $table) {
            $table->boolean('is_extend')->default(false)->after('catatan_cancel');
            $table->unsignedBigInteger('extend_from_id')->nullable()->after('is_extend');

            $table->foreign('extend_from_id')
                  ->references('id')
                  ->on('pemesanan')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('pemesanan', function (Blueprint $table) {
            $table->dropForeign(['extend_from_id']);
            $table->dropColumn(['is_extend', 'extend_from_id']);
        });
    }
};
