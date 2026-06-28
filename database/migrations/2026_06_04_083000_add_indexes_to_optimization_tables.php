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
        // 1. Table: mon_an
        Schema::table('mon_an', function (Blueprint $table) {
            $table->index(['DanhMuc', 'TrangThai', 'deleted_at'], 'idx_mon_an_danh_muc_trang_thai_deleted');
            $table->index(['MaNhaHang', 'TrangThai', 'deleted_at'], 'idx_mon_an_nha_hang_trang_thai_deleted');
        });

        // 2. Table: don_hang
        Schema::table('don_hang', function (Blueprint $table) {
            $table->index(['MaNguoiDung', 'TrangThai', 'created_at'], 'idx_don_hang_user_status_created');
            $table->index(['TrangThai', 'created_at'], 'idx_don_hang_status_created');
        });

        // 3. Table: binh_luans
        Schema::table('binh_luans', function (Blueprint $table) {
            $table->index(['ma_mon_an', 'trang_thai', 'diem_danh_gia'], 'idx_binh_luans_mon_status_rating');
        });

        // 4. Table: giam_gia
        Schema::table('giam_gia', function (Blueprint $table) {
            $table->index(['NgayBatDau', 'NgayKetThuc', 'deleted_at'], 'idx_giam_gia_dates_deleted');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Table: mon_an
        Schema::table('mon_an', function (Blueprint $table) {
            $table->dropIndex('idx_mon_an_danh_muc_trang_thai_deleted');
            $table->dropIndex('idx_mon_an_nha_hang_trang_thai_deleted');
        });

        // 2. Table: don_hang
        Schema::table('don_hang', function (Blueprint $table) {
            $table->dropIndex('idx_don_hang_user_status_created');
            $table->dropIndex('idx_don_hang_status_created');
        });

        // 3. Table: binh_luans
        Schema::table('binh_luans', function (Blueprint $table) {
            $table->dropIndex('idx_binh_luans_mon_status_rating');
        });

        // 4. Table: giam_gia
        Schema::table('giam_gia', function (Blueprint $table) {
            $table->dropIndex('idx_giam_gia_dates_deleted');
        });
    }
};
