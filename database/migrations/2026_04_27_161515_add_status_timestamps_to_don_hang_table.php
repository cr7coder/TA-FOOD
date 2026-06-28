<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('don_hang', function (Blueprint $table) {
            $table->timestamp('XacNhanAt')->nullable();
            $table->timestamp('GiaoHangAt')->nullable();
            $table->timestamp('HoanThanhAt')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('don_hang', function (Blueprint $table) {
            $table->dropColumn(['XacNhanAt', 'GiaoHangAt', 'HoanThanhAt']);
        });
    }
};
