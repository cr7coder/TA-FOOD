<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class NguoiDungSeeder extends Seeder
{
    public function run()
    {
        DB::table('nguoi_dung')->insert([
            ['TenDangNhap' => 'user1', 'MatKhau' => Hash::make('123456'), 'HoTen' => 'Nguyễn Văn A', 'Email' => 'a@example.com', 'SoDienThoai' => '0901111111', 'VaiTro' => 'KhachHang'],
            ['TenDangNhap' => 'user2', 'MatKhau' => Hash::make('123456'), 'HoTen' => 'Trần Thị B', 'Email' => 'b@example.com', 'SoDienThoai' => '0902222222', 'VaiTro' => 'NguoiBan'],
            ['TenDangNhap' => 'user3', 'MatKhau' => Hash::make('123456'), 'HoTen' => 'Lê Văn C', 'Email' => 'c@example.com', 'SoDienThoai' => '0903333333', 'VaiTro' => 'QuanTri'],
            ['TenDangNhap' => 'user4', 'MatKhau' => Hash::make('123456'), 'HoTen' => 'Phạm Thị D', 'Email' => 'd@example.com', 'SoDienThoai' => '0904444444', 'VaiTro' => 'KhachHang'],
            ['TenDangNhap' => 'user5', 'MatKhau' => Hash::make('123456'), 'HoTen' => 'Hoàng Văn E', 'Email' => 'e@example.com', 'SoDienThoai' => '0905555555', 'VaiTro' => 'NguoiBan'],
        ]);
    }
}
