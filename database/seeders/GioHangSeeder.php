<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GioHangSeeder extends Seeder
{
    public function run()
    {
        DB::table('gio_hang')->insert([
            ['MaNguoiDung' => 1],
            ['MaNguoiDung' => 2],
            ['MaNguoiDung' => 3],
            ['MaNguoiDung' => 4],
            ['MaNguoiDung' => 5],
        ]);
    }
}
