<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('mon_an', function (Blueprint $table) {
            $table->increments('MaMonAn');
            $table->integer('MaNhaHang')->unsigned();
            $table->string('TenMonAn', 150);
            $table->enum('DanhMuc', ['Cơm', 'Bún', 'Phở', 'Mì', 'Trà'])->default('Cơm');
            $table->string('MoTa', 255)->nullable();
            $table->decimal('Gia', 18, 2)->unsigned();
            $table->string('HinhAnh', 255)->nullable(); // lưu đường dẫn ảnh
            $table->enum('TrangThai', ['Còn bán', 'Ngừng bán'])->default('Còn bán');
            $table->timestamps();

            $table->foreign('MaNhaHang')
                ->references('MaNhaHang')->on('nha_hang')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mon_an');
    }
};
