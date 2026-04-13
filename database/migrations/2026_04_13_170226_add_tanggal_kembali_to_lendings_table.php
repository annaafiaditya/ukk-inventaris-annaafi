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
        Schema::table('lendings', function (Blueprint $table) {
            if (! Schema::hasColumn('lendings', 'tanggal_kembali')) {
                $table->date('tanggal_kembali')->nullable()->after('tanggal_pinjam');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lendings', function (Blueprint $table) {
            if (Schema::hasColumn('lendings', 'tanggal_kembali')) {
                $table->dropColumn('tanggal_kembali');
            }
        });
    }
};
