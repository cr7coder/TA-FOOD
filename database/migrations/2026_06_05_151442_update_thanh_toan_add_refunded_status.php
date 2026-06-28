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
            DB::statement("ALTER TABLE thanh_toan MODIFY COLUMN TrangThai ENUM('Chờ thanh toán','Chờ xác nhận','Đã thanh toán','Thất bại','Đã hoàn tiền') DEFAULT 'Chờ thanh toán'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE thanh_toan MODIFY COLUMN TrangThai ENUM('Chờ thanh toán','Chờ xác nhận','Đã thanh toán','Thất bại') DEFAULT 'Chờ thanh toán'");
        }
    }
};
