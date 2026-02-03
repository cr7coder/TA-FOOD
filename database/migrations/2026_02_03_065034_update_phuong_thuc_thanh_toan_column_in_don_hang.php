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
        DB::statement("ALTER TABLE don_hang MODIFY COLUMN PhuongThucThanhToan ENUM('COD', 'MoMo', 'ZaloPay', 'VNPay', 'ShopeePay', 'Online', 'VISA', 'Mastercard', 'ATM') DEFAULT 'COD'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE don_hang MODIFY COLUMN PhuongThucThanhToan ENUM('COD', 'Online') DEFAULT 'COD'");
    }
};
