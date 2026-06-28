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
        Schema::create('thanh_toan', function (Blueprint $table) {
            $table->increments('MaThanhToan');
            $table->unsignedInteger('MaDonHang');
            $table->decimal('SoTien', 18, 2)->unsigned();
            $table->enum('PhuongThuc', ['COD', 'Online', 'MoMo', 'ZaloPay', 'VNPay', 'ShopeePay', 'VietQR', 'VISA', 'Mastercard', 'ATM'])->default('COD');
            $table->enum('TrangThai', ['Chờ thanh toán', 'Chờ xác nhận', 'Đã thanh toán', 'Thất bại', 'Đã hoàn tiền'])->default('Chờ thanh toán');
            $table->string('MaGiaoDich', 100)->nullable();
            $table->timestamps();

            // Foreign key
            $table->foreign('MaDonHang')
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
        Schema::dropIfExists('thanh_toan');
    }
};