<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            // 1. Temporarily allow BOTH 'Đã thanh toán' and 'Đã xác nhận' in the enum
            DB::statement("ALTER TABLE don_hang MODIFY COLUMN TrangThai ENUM('Chờ xử lý', 'Đã thanh toán', 'Đã xác nhận', 'Đang chuẩn bị', 'Đang giao', 'Hoàn thành', 'Hủy') NOT NULL DEFAULT 'Chờ xử lý'");

            // 2. Update existing orders
            DB::statement("UPDATE don_hang SET TrangThai = 'Đã xác nhận' WHERE TrangThai = 'Đã thanh toán'");

            // 3. Modify enum to drop 'Đã thanh toán'
            DB::statement("ALTER TABLE don_hang MODIFY COLUMN TrangThai ENUM('Chờ xử lý', 'Đã xác nhận', 'Đang chuẩn bị', 'Đang giao', 'Hoàn thành', 'Hủy') NOT NULL DEFAULT 'Chờ xử lý'");
        } else {
            // For SQLite, we just update the table data without altering table structure since SQLite handles enum as text
            DB::statement("UPDATE don_hang SET TrangThai = 'Đã xác nhận' WHERE TrangThai = 'Đã thanh toán'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            // 1. Temporarily allow both values
            DB::statement("ALTER TABLE don_hang MODIFY COLUMN TrangThai ENUM('Chờ xử lý', 'Đã thanh toán', 'Đã xác nhận', 'Đang chuẩn bị', 'Đang giao', 'Hoàn thành', 'Hủy') NOT NULL DEFAULT 'Chờ xử lý'");

            // 2. Update back
            DB::statement("UPDATE don_hang SET TrangThai = 'Đã thanh toán' WHERE TrangThai = 'Đã xác nhận'");

            // 3. Drop 'Đã xác nhận' from enum
            DB::statement("ALTER TABLE don_hang MODIFY COLUMN TrangThai ENUM('Chờ xử lý', 'Đã thanh toán', 'Đang chuẩn bị', 'Đang giao', 'Hoàn thành', 'Hủy') NOT NULL DEFAULT 'Chờ xử lý'");
        } else {
            DB::statement("UPDATE don_hang SET TrangThai = 'Đã thanh toán' WHERE TrangThai = 'Đã xác nhận'");
        }
    }
};
