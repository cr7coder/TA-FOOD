<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('danh_mucs', function (Blueprint $table) {
            $table->string('Slug', 100)->after('TenDanhMuc')->nullable();
        });

        // Populate slug for existing categories
        $categories = \Illuminate\Support\Facades\DB::table('danh_mucs')->get();
        foreach ($categories as $c) {
            $slug = Str::slug($c->TenDanhMuc, '-');
            \Illuminate\Support\Facades\DB::table('danh_mucs')
                ->where('MaDanhMuc', $c->MaDanhMuc)
                ->update(['Slug' => $slug]);
        }

        // Change to unique now that it is populated
        Schema::table('danh_mucs', function (Blueprint $table) {
            $table->string('Slug', 100)->nullable(false)->unique()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('danh_mucs', function (Blueprint $table) {
            $table->dropColumn('Slug');
        });
    }
};
