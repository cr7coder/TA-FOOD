<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class GiamGiaSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now()->toDateTimeString();

        DB::table('giam_gia')->insert([
            [
                'MaCode' => 'SALE10',
                'LoaiGiamGia' => 'PhanTram',
                'PhanTram' => 10.00,
                'GiamToiDa' => 50000.00,
                'DonHangToiThieu' => 50000.00,
                'NgayBatDau' => '2026-01-01',
                'NgayKetThuc' => '2026-12-31',
                'SoLuongToiDa' => 100,
                'SoLuongDaSuDung' => 0,
                'MoTa' => 'Giảm 10% cho đơn hàng từ 50k',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'MaCode' => 'SALE50',
                'LoaiGiamGia' => 'PhanTram',
                'PhanTram' => 50.00,
                'GiamToiDa' => 100000.00,
                'DonHangToiThieu' => 200000.00,
                'NgayBatDau' => '2026-03-01',
                'NgayKetThuc' => '2026-06-30',
                'SoLuongToiDa' => 50,
                'SoLuongDaSuDung' => 0,
                'MoTa' => 'Giảm 50% tối đa 100k cho đơn từ 200k',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'MaCode' => 'FREESHIP',
                'LoaiGiamGia' => 'TienMat',
                'PhanTram' => 0.00,
                'GiamToiDa' => 30000.00,
                'DonHangToiThieu' => 0.00,
                'NgayBatDau' => '2026-02-01',
                'NgayKetThuc' => '2026-05-31',
                'SoLuongToiDa' => null,
                'SoLuongDaSuDung' => 0,
                'MoTa' => 'Miễn phí ship tối đa 30k',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'MaCode' => 'VIP20',
                'LoaiGiamGia' => 'PhanTram',
                'PhanTram' => 20.00,
                'GiamToiDa' => 80000.00,
                'DonHangToiThieu' => 100000.00,
                'NgayBatDau' => '2026-05-01',
                'NgayKetThuc' => '2026-12-31',
                'SoLuongToiDa' => 200,
                'SoLuongDaSuDung' => 0,
                'MoTa' => 'Giảm 20% tối đa 80k cho đơn từ 100k',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'MaCode' => 'NEWUSER',
                'LoaiGiamGia' => 'TienMat',
                'PhanTram' => 0.00,
                'GiamToiDa' => 50000.00,
                'DonHangToiThieu' => 0.00,
                'NgayBatDau' => '2026-01-01',
                'NgayKetThuc' => '2026-04-30',
                'SoLuongToiDa' => 1000,
                'SoLuongDaSuDung' => 0,
                'MoTa' => 'Giảm 50k cho người dùng mới',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
