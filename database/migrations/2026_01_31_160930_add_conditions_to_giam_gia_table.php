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
        Schema::table('giam_gia', function (Blueprint $table) {
            $table->integer('SoLuongToiDa')->nullable()->after('DonHangToiThieu')->comment('Số lượng voucher tối đa');
            $table->integer('SoLuongDaSuDung')->default(0)->after('SoLuongToiDa')->comment('Số lượng đã sử dụng');
            $table->enum('LoaiGiamGia', ['Phần trăm', 'Số tiền'])->default('Phần trăm')->after('MaCode');
            $table->decimal('GiamToiDa', 18, 2)->nullable()->after('PhanTram')->comment('Giảm tối đa (cho loại phần trăm)');
            $table->text('MoTa')->nullable()->after('NgayKetThuc');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('giam_gia', function (Blueprint $table) {
            $table->dropColumn([
                'SoLuongToiDa', 
                'SoLuongDaSuDung', 
                'LoaiGiamGia', 
                'GiamToiDa',
                'MoTa'
            ]);
        });
    }
};