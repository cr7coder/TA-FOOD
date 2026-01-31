<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('nguoi_dung', function (Blueprint $table) {
            $table->increments('MaNguoiDung'); // int unsigned auto increment
            $table->string('TenDangNhap', 100)->unique();
            $table->string('MatKhau', 255);
            $table->string('HoTen', 150)->nullable();
            $table->string('Email', 150)->unique();
            $table->string('SoDienThoai', 20)->nullable();
            $table->enum('VaiTro', ['KhachHang', 'QuanTri', 'NguoiBan'])->default('KhachHang');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nguoi_dung');
    }
};
