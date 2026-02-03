<?php

namespace Database\Factories;

use App\Models\NhaHang;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class NhaHangFactory extends Factory
{
    protected $model = NhaHang::class;

    public function definition(): array
    {
        return [
            'TenNhaHang' => fake()->company(),
            'DiaChi' => fake()->address(),
            'SoDienThoai' => '0' . fake()->numerify('#########'),
            'GioMoCua' => '08:00:00',
            'GioDongCua' => '22:00:00',
            'MaNguoiDung' => User::factory(),
        ];
    }
}
