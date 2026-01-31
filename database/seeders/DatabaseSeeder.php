<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $this->call([
            NguoiDungSeeder::class,
            NhaHangSeeder::class,
            MonAnSeeder::class,
            GiamGiaSeeder::class,
            DoiTacVanChuyenSeeder::class,
            GioHangSeeder::class,
            GioHangChiTietSeeder::class,
            // DonHangSeeder::class,
            // DonHangChiTietSeeder::class,
            // ThanhToanSeeder::class,
        ]);
    }
}