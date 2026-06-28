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
        Schema::create('mon_an_yeu_thich', function (Blueprint $table) {
            $table->unsignedInteger('MaNguoiDung');
            $table->unsignedInteger('MaMonAn');
            $table->timestamps();

            $table->primary(['MaNguoiDung', 'MaMonAn']);
            $table->foreign('MaNguoiDung')->references('MaNguoiDung')->on('nguoi_dung')->onDelete('cascade');
            $table->foreign('MaMonAn')->references('MaMonAn')->on('mon_an')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mon_an_yeu_thich');
    }
};
