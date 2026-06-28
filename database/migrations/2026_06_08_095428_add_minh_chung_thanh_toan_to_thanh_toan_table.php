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
        Schema::table('thanh_toan', function (Blueprint $table) {
            $table->string('minh_chung_thanh_toan', 255)->nullable()->after('MaGiaoDich');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('thanh_toan', function (Blueprint $table) {
            $table->dropColumn('minh_chung_thanh_toan');
        });
    }
};
