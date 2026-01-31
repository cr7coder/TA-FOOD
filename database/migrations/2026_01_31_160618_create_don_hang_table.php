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
        Schema::create('don_hang', function (Blueprint $table) {
            $table->increments('MaDonHang');
            $table->unsignedInteger('MaNguoiDung');
            $table->unsignedInteger('MaGiamGia')->nullable();
            $table->decimal('TongTien', 18, 2)->unsigned();
            $table->enum('PhuongThucThanhToan', ['COD', 'Online'])->default('COD');
            $table->enum('TrangThai', [
                'Chờ xử lý', 
                'Đã thanh toán', 
                'Đang chuẩn bị', 
                'Đang giao', 
                'Hoàn thành', 
                'Hủy'
            ])->default('Chờ xử lý');
            $table->string('TenKhachHang');
            $table->string('SoDienThoai', 20);
            $table->string('DiaChiGiaoHang');
            $table->text('GhiChu')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('MaNguoiDung')
                ->references('MaNguoiDung')
                ->on('nguoi_dung')
                ->onDelete('cascade');

            $table->foreign('MaGiamGia')
                ->references('MaGiamGia')
                ->on('giam_gia')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('don_hang');
    }
};