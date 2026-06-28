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
        Schema::table('doi_tac_van_chuyens', function (Blueprint $table) {
            $table->string('nguoi_lien_he', 255)->nullable()->after('ten_doi_tac');
            $table->decimal('phi_km', 10, 2)->default(0)->after('phi_van_chuyen');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('doi_tac_van_chuyens', function (Blueprint $table) {
            $table->dropColumn(['nguoi_lien_he', 'phi_km']);
        });
    }
};
