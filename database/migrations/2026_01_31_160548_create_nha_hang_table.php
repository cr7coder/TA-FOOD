<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('nha_hang', function (Blueprint $table) {
            $table->increments('MaNhaHang');
            $table->string('TenNhaHang', 150);
            $table->string('DiaChi', 255);
            $table->string('SoDienThoai', 20)->nullable();
            $table->time('GioMoCua')->nullable();
            $table->time('GioDongCua')->nullable();

            $table->unsignedInteger('MaNguoiDung')->nullable();
            $table->foreign('MaNguoiDung')
                ->references('MaNguoiDung')->on('nguoi_dung') // đổi 'nguoi_dung' nếu bảng user khác tên
                ->nullOnDelete(); // tương đương ON DELETE SET NULL

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nha_hang');
    }
};