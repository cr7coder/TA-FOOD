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
        Schema::table('mon_an', function (Blueprint $table) {
            $table->string('DanhMuc', 100)->default('Cơm')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mon_an', function (Blueprint $table) {
            $table->enum('DanhMuc', ['Cơm', 'Bún', 'Phở', 'Mì', 'Trà'])->default('Cơm')->change();
        });
    }
};
