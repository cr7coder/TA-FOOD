<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('nha_hang', function (Blueprint $table) {
            // Commission rate admin charges seller (% of food revenue), default 15%
            $table->decimal('commission_rate', 5, 2)->default(15.00)->after('MaNguoiDung');
        });
    }

    public function down(): void
    {
        Schema::table('nha_hang', function (Blueprint $table) {
            $table->dropColumn('commission_rate');
        });
    }
};
