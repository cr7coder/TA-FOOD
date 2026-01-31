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
        Schema::create('don_hang_chi_tiet', function (Blueprint $table) {
            $table->increments('MaChiTiet');
            $table->unsignedInteger('MaDonHang');
            $table->unsignedInteger('MaMonAn');
            $table->integer('SoLuong')->unsigned()->default(1);
            $table->decimal('Gia', 18, 2)->unsigned();

            // Foreign keys
            $table->foreign('MaDonHang')
                ->references('MaDonHang')
                ->on('don_hang')
                ->onDelete('cascade');

            $table->foreign('MaMonAn')
                ->references('MaMonAn')
                ->on('mon_an')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('don_hang_chi_tiet');
    }
};