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
        Schema::create('danh_mucs', function (Blueprint $table) {
            $table->increments('MaDanhMuc');
            $table->string('TenDanhMuc', 100)->unique();
            $table->string('MoTa', 255)->nullable();
            $table->string('HinhAnh', 255)->nullable();
            $table->string('TrangThai', 50)->default('Hoạt động');
            $table->timestamps();
        });

        // Seed default categories
        $defaults = [
            ['TenDanhMuc' => 'Cơm', 'MoTa' => 'Các món cơm bình dân, cơm văn phòng thơm ngon', 'HinhAnh' => '/images/categories/com.png', 'TrangThai' => 'Hoạt động', 'created_at' => now(), 'updated_at' => now()],
            ['TenDanhMuc' => 'Bún', 'MoTa' => 'Bún bò, bún riêu, bún chả các loại', 'HinhAnh' => '/images/categories/bun.png', 'TrangThai' => 'Hoạt động', 'created_at' => now(), 'updated_at' => now()],
            ['TenDanhMuc' => 'Phở', 'MoTa' => 'Phở bò, phở gà truyền thống đậm vị', 'HinhAnh' => '/images/categories/pho.png', 'TrangThai' => 'Hoạt động', 'created_at' => now(), 'updated_at' => now()],
            ['TenDanhMuc' => 'Mì', 'MoTa' => 'Mì Quảng, mì xào, mì ramen hấp dẫn', 'HinhAnh' => '/images/categories/mi.png', 'TrangThai' => 'Hoạt động', 'created_at' => now(), 'updated_at' => now()],
            ['TenDanhMuc' => 'Trà', 'MoTa' => 'Trà sữa, trà chanh, nước giải khát thanh mát', 'HinhAnh' => '/images/categories/tra.png', 'TrangThai' => 'Hoạt động', 'created_at' => now(), 'updated_at' => now()],
            ['TenDanhMuc' => 'Khác', 'MoTa' => 'Các món ăn vặt và đồ uống giải khát khác', 'HinhAnh' => '/images/categories/khac.png', 'TrangThai' => 'Hoạt động', 'created_at' => now(), 'updated_at' => now()],
        ];
        \Illuminate\Support\Facades\DB::table('danh_mucs')->insert($defaults);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('danh_mucs');
    }
};
