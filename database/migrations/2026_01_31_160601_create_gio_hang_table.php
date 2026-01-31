<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('gio_hang', function (Blueprint $table) {
            $table->increments('MaGioHang');
            $table->integer('MaNguoiDung')->unsigned();
            $table->timestamps();

            $table->foreign('MaNguoiDung')
                ->references('MaNguoiDung')->on('nguoi_dung')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gio_hang');
    }
};
