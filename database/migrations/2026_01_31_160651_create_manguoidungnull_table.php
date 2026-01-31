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
        // Cho phép MaNguoiDung nullable trong gio_hang
        Schema::table('gio_hang', function (Blueprint $table) {
            $table->dropForeign(['MaNguoiDung']);
            $table->unsignedInteger('MaNguoiDung')->nullable()->change();
            
            $table->foreign('MaNguoiDung')
                ->references('MaNguoiDung')
                ->on('nguoi_dung')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gio_hang', function (Blueprint $table) {
            $table->dropForeign(['MaNguoiDung']);
            $table->unsignedInteger('MaNguoiDung')->nullable(false)->change();
            
            $table->foreign('MaNguoiDung')
                ->references('MaNguoiDung')
                ->on('nguoi_dung')
                ->onDelete('cascade');
        });
    }
};