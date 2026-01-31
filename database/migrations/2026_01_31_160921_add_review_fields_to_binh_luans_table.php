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
        Schema::table('binh_luans', function (Blueprint $table) {
            $table->boolean('da_mua')->default(false)->after('diem_danh_gia')->comment('User đã mua món này chưa');
            $table->enum('trang_thai', ['Chờ duyệt', 'Đã duyệt', 'Từ chối'])->default('Đã duyệt')->after('hinh_anh');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('binh_luans', function (Blueprint $table) {
            $table->dropColumn(['da_mua', 'trang_thai']);
        });
    }
};