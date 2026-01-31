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
        Schema::create('binh_luans', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('ma_mon_an');
            $table->unsignedInteger('ma_nguoi_dung');
            $table->integer('diem_danh_gia')->comment('Điểm đánh giá từ 1-5 sao');
            $table->text('noi_dung')->nullable();
            $table->string('hinh_anh')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('ma_mon_an')
                ->references('MaMonAn')
                ->on('mon_an')
                ->onDelete('cascade');

            $table->foreign('ma_nguoi_dung')
                ->references('MaNguoiDung')
                ->on('nguoi_dung')
                ->onDelete('cascade');

            // Indexes
            $table->index(['ma_mon_an', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('binh_luans');
    }
};