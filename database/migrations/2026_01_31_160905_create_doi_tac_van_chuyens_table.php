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
        Schema::create('doi_tac_van_chuyens', function (Blueprint $table) {
            $table->id();
            $table->string('ten_doi_tac', 255);
            $table->string('so_dien_thoai', 20);
            $table->text('dia_chi_tru_so')->nullable();
            $table->string('email_lien_he', 255)->unique();
            $table->decimal('phi_van_chuyen', 10, 2)->default(0);
            $table->enum('trang_thai', ['Hoạt động', 'Ngừng hoạt động'])->default('Hoạt động');
            $table->timestamps();
        });

        // Thêm cột MaDoiTacVanChuyen vào bảng don_hang
        Schema::table('don_hang', function (Blueprint $table) {
            $table->unsignedBigInteger('MaDoiTacVanChuyen')->nullable()->after('MaGiamGia');
            $table->foreign('MaDoiTacVanChuyen')
                ->references('id')
                ->on('doi_tac_van_chuyens')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('don_hang', function (Blueprint $table) {
            $table->dropForeign(['MaDoiTacVanChuyen']);
            $table->dropColumn('MaDoiTacVanChuyen');
        });

        Schema::dropIfExists('doi_tac_van_chuyens');
    }
};