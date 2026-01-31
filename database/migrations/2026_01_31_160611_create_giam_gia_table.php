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
        Schema::create('giam_gia', function (Blueprint $table) {
            $table->increments('MaGiamGia');
            $table->string('MaCode', 50)->unique();
            $table->enum('LoaiGiamGia', ['PhanTram', 'TienMat'])->default('PhanTram');
            $table->decimal('PhanTram', 5, 2)->default(0);
            $table->decimal('GiamToiDa', 10, 2)->nullable();
            $table->decimal('DonHangToiThieu', 10, 2)->default(0);
            $table->date('NgayBatDau');
            $table->date('NgayKetThuc');
            $table->integer('SoLuongToiDa')->nullable();
            $table->integer('SoLuongDaSuDung')->default(0);
            $table->text('MoTa')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('giam_gia');
    }
};