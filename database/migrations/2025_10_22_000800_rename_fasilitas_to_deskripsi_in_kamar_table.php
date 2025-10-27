<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('kamar', function (Blueprint $table) {
            if (! Schema::hasColumn('kamar', 'deskripsi')) {
                $table->text('deskripsi')->nullable()->after('harga');
            }
        });

        // Copy existing data from `fasilitas` to `deskripsi` if present
        try {
            DB::statement('UPDATE kamar SET deskripsi = COALESCE(deskripsi, fasilitas)');
        } catch (\Throwable $e) {
            // ignore if table empty or column missing in some env
        }

        // Drop old column if exists
        Schema::table('kamar', function (Blueprint $table) {
            if (Schema::hasColumn('kamar', 'fasilitas')) {
                $table->dropColumn('fasilitas');
            }
        });
    }

    public function down(): void
    {
        Schema::table('kamar', function (Blueprint $table) {
            if (! Schema::hasColumn('kamar', 'fasilitas')) {
                $table->text('fasilitas')->nullable()->after('harga');
            }
        });

        try {
            DB::statement('UPDATE kamar SET fasilitas = COALESCE(fasilitas, deskripsi)');
        } catch (\Throwable $e) {}

        Schema::table('kamar', function (Blueprint $table) {
            if (Schema::hasColumn('kamar', 'deskripsi')) {
                $table->dropColumn('deskripsi');
            }
        });
    }
};

