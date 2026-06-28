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
            $table->dropForeign(['ma_mon_an']);
            $table->dropForeign(['ma_nguoi_dung']);
            $table->dropForeign(['ma_don_hang']);
        });

        Schema::table('binh_luans', function (Blueprint $table) {
            $table->renameColumn('ma_mon_an', 'MaMonAn');
            $table->renameColumn('ma_nguoi_dung', 'MaNguoiDung');
            $table->renameColumn('ma_don_hang', 'MaDonHang');
        });

        Schema::table('binh_luans', function (Blueprint $table) {
            $table->foreign('MaMonAn')->references('MaMonAn')->on('mon_an')->onDelete('cascade');
            $table->foreign('MaNguoiDung')->references('MaNguoiDung')->on('nguoi_dung')->onDelete('cascade');
            $table->foreign('MaDonHang')->references('MaDonHang')->on('don_hang')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('binh_luans', function (Blueprint $table) {
            $table->dropForeign(['MaMonAn']);
            $table->dropForeign(['MaNguoiDung']);
            $table->dropForeign(['MaDonHang']);
        });

        Schema::table('binh_luans', function (Blueprint $table) {
            $table->renameColumn('MaMonAn', 'ma_mon_an');
            $table->renameColumn('MaNguoiDung', 'ma_nguoi_dung');
            $table->renameColumn('MaDonHang', 'ma_don_hang');
        });

        Schema::table('binh_luans', function (Blueprint $table) {
            $table->foreign('ma_mon_an')->references('MaMonAn')->on('mon_an')->onDelete('cascade');
            $table->foreign('ma_nguoi_dung')->references('MaNguoiDung')->on('nguoi_dung')->onDelete('cascade');
            $table->foreign('ma_don_hang')->references('MaDonHang')->on('don_hang')->onDelete('cascade');
        });
    }
};
