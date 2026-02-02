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
            $table->unsignedInteger('ma_don_hang')->nullable()->after('ma_nguoi_dung');
            
            // Foreign key
            $table->foreign('ma_don_hang')
                ->references('MaDonHang')
                ->on('don_hang')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('binh_luans', function (Blueprint $table) {
            $table->dropForeign(['ma_don_hang']);
            $table->dropColumn('ma_don_hang');
        });
    }
};
