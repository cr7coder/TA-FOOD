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
        Schema::table('gio_hang', function (Blueprint $table) {
            // Thêm cột session_id để lưu giỏ hàng cho guest users
            $table->string('session_id', 255)->nullable()->after('MaNguoiDung');
            $table->index('session_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gio_hang', function (Blueprint $table) {
            $table->dropIndex(['session_id']);
            $table->dropColumn('session_id');
        });
    }
};