<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('nguoi_dung', function (Blueprint $table) {
            $table->string('DiaChiNhaRieng', 500)->nullable()->after('DiaChi')->comment('Địa chỉ nhà riêng');
            $table->string('DiaChiVanPhong', 500)->nullable()->after('DiaChiNhaRieng')->comment('Địa chỉ văn phòng');
            $table->string('DiaChiTruongHoc', 500)->nullable()->after('DiaChiVanPhong')->comment('Địa chỉ trường học');
        });
    }

    public function down(): void
    {
        Schema::table('nguoi_dung', function (Blueprint $table) {
            $table->dropColumn(['DiaChiNhaRieng', 'DiaChiVanPhong', 'DiaChiTruongHoc']);
        });
    }
};
