<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GioHangChiTietSeeder extends Seeder
{
    public function run()
    {
        DB::table('gio_hang_chi_tiet')->insert([
            ['MaGioHang' => 1, 'MaMonAn' => 1, 'SoLuong' => 1],
            ['MaGioHang' => 1, 'MaMonAn' => 2, 'SoLuong' => 2],
            ['MaGioHang' => 2, 'MaMonAn' => 3, 'SoLuong' => 1],
            ['MaGioHang' => 3, 'MaMonAn' => 4, 'SoLuong' => 1],
            ['MaGioHang' => 4, 'MaMonAn' => 5, 'SoLuong' => 2],
        ]);
    }
}
