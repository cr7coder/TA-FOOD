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
            $table->decimal('PhanTram', 5, 2)->unsigned();
            $table->date('NgayBatDau');
            $table->date('NgayKetThuc');
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