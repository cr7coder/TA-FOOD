<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('gio_hang_chi_tiet', function (Blueprint $table) {
            $table->increments('MaChiTiet');
            $table->integer('MaGioHang')->unsigned();
            $table->integer('MaMonAn')->unsigned();
            $table->integer('SoLuong')->unsigned()->default(1);

            $table->foreign('MaGioHang')
                ->references('MaGioHang')->on('gio_hang')
                ->onDelete('cascade');

            $table->foreign('MaMonAn')
                ->references('MaMonAn')->on('mon_an')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gio_hang_chi_tiet');
    }
};
