<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('nha_hang', function (Blueprint $table) {
            $table->decimal('latitude', 10, 8)->nullable()->default(21.081827)->after('DiaChi');
            $table->decimal('longitude', 11, 8)->nullable()->default(105.842790)->after('latitude');
            $table->decimal('phi_ship_co_ban', 10, 2)->default(15000)->after('longitude');
            $table->decimal('phi_ship_moi_km', 10, 2)->default(5000)->after('phi_ship_co_ban');
            $table->decimal('km_mien_phi', 5, 2)->default(2.00)->after('phi_ship_moi_km');
        });
    }

    public function down(): void
    {
        Schema::table('nha_hang', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude', 'phi_ship_co_ban', 'phi_ship_moi_km', 'km_mien_phi']);
        });
    }
};
