<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Thêm cột NgayThanhToan nếu chưa có
        if (!Schema::hasColumn('thanh_toan', 'NgayThanhToan')) {
            Schema::table('thanh_toan', function (Blueprint $table) {
                $table->timestamp('NgayThanhToan')->nullable()->after('MaGiaoDich');
            });
        }

        // 2. Thêm cột payos_order_code để lưu nội dung chuyển khoản
        if (!Schema::hasColumn('thanh_toan', 'payos_order_code')) {
            Schema::table('thanh_toan', function (Blueprint $table) {
                $table->string('payos_order_code', 100)->nullable()->after('NgayThanhToan');
            });
        }

        // 3. Sửa enum TrangThai để thêm 'Chờ xác nhận'
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE thanh_toan MODIFY COLUMN TrangThai ENUM('Chờ thanh toán','Chờ xác nhận','Đã thanh toán','Thất bại') DEFAULT 'Chờ thanh toán'");
        }

        // 4. Sửa enum PhuongThuc để thêm 'VietQR'
        // Trước tiên cập nhật dữ liệu cũ không hợp lệ sang 'Online'
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("UPDATE thanh_toan SET PhuongThuc = 'Online' WHERE PhuongThuc NOT IN ('COD','Online','MoMo','ZaloPay','VNPay','ShopeePay')");
            DB::statement("ALTER TABLE thanh_toan MODIFY COLUMN PhuongThuc ENUM('COD','Online','MoMo','ZaloPay','VNPay','ShopeePay','VietQR') DEFAULT 'COD'");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE thanh_toan MODIFY COLUMN TrangThai ENUM('Chờ thanh toán','Đã thanh toán','Thất bại') DEFAULT 'Chờ thanh toán'");
            DB::statement("ALTER TABLE thanh_toan MODIFY COLUMN PhuongThuc ENUM('COD','Online','MoMo','ZaloPay','VNPay','ShopeePay') DEFAULT 'COD'");
        }
    }
};
