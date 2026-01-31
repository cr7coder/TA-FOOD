<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MonAnSeeder extends Seeder
{
    public function run()
    {
        DB::table('mon_an')->insert([
            [
                'MaNhaHang' => 1,
                'TenMonAn' => 'Cơm gà xối mỡ',
                'MoTa' => 'Cơm chiên vàng giòn với gà xối mỡ thơm ngon',
                'Gia' => 45000,
                'HinhAnh' => 's1.png',
                'TrangThai' => 'Còn bán',
                'DanhMuc' => 'Cơm',
            ],
            [
                'MaNhaHang' => 1,
                'TenMonAn' => 'Cơm tấm sườn bì chả',
                'MoTa' => 'Cơm tấm truyền thống với sườn nướng, bì, chả trứng',
                'Gia' => 50000,
                'HinhAnh' => 's2.png',
                'TrangThai' => 'Còn bán',
                'DanhMuc' => 'Cơm',
            ],
            [
                'MaNhaHang' => 2,
                'TenMonAn' => 'Bún bò Huế',
                'MoTa' => 'Bún bò Huế đậm đà hương vị miền Trung',
                'Gia' => 55000,
                'HinhAnh' => 's3.png',
                'TrangThai' => 'Còn bán',
                'DanhMuc' => 'Bún',
            ],
            [
                'MaNhaHang' => 2,
                'TenMonAn' => 'Bún chả Hà Nội',
                'MoTa' => 'Bún chả thịt nướng đặc sản Hà Nội',
                'Gia' => 60000,
                'HinhAnh' => 's4.png',
                'TrangThai' => 'Còn bán',
                'DanhMuc' => 'Bún',
            ],
            [
                'MaNhaHang' => 2,
                'TenMonAn' => 'Phở bò tái chín',
                'MoTa' => 'Phở bò truyền thống với thịt tái và chín',
                'Gia' => 55000,
                'HinhAnh' => 's5.png',
                'TrangThai' => 'Còn bán',
                'DanhMuc' => 'Phở',
            ],
            [
                'MaNhaHang' => 2,
                'TenMonAn' => 'Phở gà ta',
                'MoTa' => 'Phở gà thơm ngon, thanh ngọt',
                'Gia' => 50000,
                'HinhAnh' => 's6.png',
                'TrangThai' => 'Còn bán',
                'DanhMuc' => 'Phở',
            ],
            [
                'MaNhaHang' => 1,
                'TenMonAn' => 'Mì Quảng',
                'MoTa' => 'Mì Quảng đậm đà hương vị Quảng Nam',
                'Gia' => 50000,
                'HinhAnh' => 's7.png',
                'TrangThai' => 'Còn bán',
                'DanhMuc' => 'Mì',
            ],
            [
                'MaNhaHang' => 1,
                'TenMonAn' => 'Mì xào hải sản',
                'MoTa' => 'Mì xào thơm ngon với tôm, mực, rau củ',
                'Gia' => 65000,
                'HinhAnh' => 's8.png',
                'TrangThai' => 'Còn bán',
                'DanhMuc' => 'Mì',
            ],
            [
                'MaNhaHang' => 2,
                'TenMonAn' => 'Trà sữa trân châu',
                'MoTa' => 'Trà sữa béo ngậy kèm trân châu dai ngon',
                'Gia' => 35000,
                'HinhAnh' => 's9.png',
                'TrangThai' => 'Còn bán',
                'DanhMuc' => 'Trà',
            ],
            [
                'MaNhaHang' => 1,
                'TenMonAn' => 'Trà đào cam sả',
                'MoTa' => 'Trà đào thanh mát kết hợp cam và sả',
                'Gia' => 40000,
                'HinhAnh' => 's10.png',
                'TrangThai' => 'Còn bán',
                'DanhMuc' => 'Trà',
            ],
        ]);
    }
}
