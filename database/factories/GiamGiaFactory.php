<?php

namespace Database\Factories;

use App\Models\GiamGia;
use Illuminate\Database\Eloquent\Factories\Factory;

class GiamGiaFactory extends Factory
{
    protected $model = GiamGia::class;

    public function definition(): array
    {
        return [
            'MaCode' => strtoupper($this->faker->unique()->lexify('??????')),
            'LoaiGiamGia' => 'PhanTram',
            'PhanTram' => $this->faker->randomFloat(2, 5, 50),
            'MoTa' => $this->faker->sentence(),
            'NgayBatDau' => now()->subDays(1)->format('Y-m-d'),
            'NgayKetThuc' => now()->addDays(30)->format('Y-m-d'),
        ];
    }
}
