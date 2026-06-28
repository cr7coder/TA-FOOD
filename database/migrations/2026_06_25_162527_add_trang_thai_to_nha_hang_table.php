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
        Schema::table('nha_hang', function (Blueprint $table) {
            $table->string('TrangThai', 50)->default('Chờ duyệt')->after('HinhAnh');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nha_hang', function (Blueprint $table) {
            $table->dropColumn('TrangThai');
        });
    }
};
