<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NhaHangSeeder extends Seeder
{
    public function run()
    {
        DB::table('nha_hang')->insert([
            [
                'TenNhaHang' => 'Nhà hàng Cơm Tấm Sài Gòn',
                'DiaChi' => 'Ngõ 62 Trần Thái Tông, Cầu Giấy, Hà Nội',
                'SoDienThoai' => '0901234567',
                'GioMoCua' => '10:00',
                'GioDongCua' => '22:00',
                'MaNguoiDung' => 5,
            ],
            [
                'TenNhaHang' => 'Bún Chả Hà Nội',
                'DiaChi' => '45 Trần Hưng Đạo, Hà Nội',
                'SoDienThoai' => '0987654321',
                'GioMoCua' => '09:00',
                'GioDongCua' => '21:00',
                'MaNguoiDung' => 2,
            ],
            // [
            //     'TenNhaHang' => 'Phở Thìn',
            //     'DiaChi' => '13 Lò Đúc, Hà Nội',
            //     'SoDienThoai' => '0912345678',
            //     'MaNguoiDung' => "",
            // ],
            // [
            //     'TenNhaHang' => 'Mì Quảng Đà Nẵng',
            //     'DiaChi' => '16, ngõ 298 Tây Sơn, Ngã Tư Sở, Đống Đa, Hà Nộ',
            //     'SoDienThoai' => '0938123456',
            //     'MaNguoiDung' => "",
            // ],
            // [
            //     'TenNhaHang' => 'Trà Sữa Gong Cha',
            //     'DiaChi' => '66 Tô Hiến Thành, Quận Hai Bà Trưng, Hà Nội',
            //     'SoDienThoai' => '0909090909',
            //     'MaNguoiDung' => "",
            // ],
        ]);
    }
}
