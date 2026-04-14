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
            $table->integer('total_dikembalikan')->default(0)->after('total');
            $table->integer('rusak')->default(0)->after('total_dikembalikan');
            $table->integer('hilang')->default(0)->after('rusak');
            $table->text('return_note')->nullable()->after('hilang');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lendings', function (Blueprint $table) {
            $table->dropColumn(['total_dikembalikan', 'rusak', 'hilang', 'return_note']);
        });
    }
};
