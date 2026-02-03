<?php

namespace Database\Factories;

use App\Models\MonAn;
use App\Models\NhaHang;
use Illuminate\Database\Eloquent\Factories\Factory;

class MonAnFactory extends Factory
{
    protected $model = MonAn::class;

    public function definition(): array
    {
        $categories = ['Cơm', 'Bún', 'Phở', 'Mì', 'Trà'];
        $foodNames = [
            'Cơm' => ['Cơm Gà', 'Cơm Sườn', 'Cơm Tấm'],
            'Bún' => ['Bún Bò Huế', 'Bún Chả', 'Bún Riêu'],
            'Phở' => ['Phở Bò', 'Phở Gà', 'Phở Tái'],
            'Mì' => ['Mì Xào', 'Mì Quảng', 'Mì Hoành Thánh'],
            'Trà' => ['Trà Sữa', 'Trà Đào', 'Trà Chanh'],
        ];

        $category = fake()->randomElement($categories);
        $foodName = fake()->randomElement($foodNames[$category]);

        return [
            'MaNhaHang' => NhaHang::factory(),
            'TenMonAn' => $foodName,
            'DanhMuc' => $category,
            'MoTa' => fake()->sentence(),
            'Gia' => fake()->numberBetween(15000, 100000),
            'HinhAnh' => fake()->optional()->imageUrl(640, 480, 'food'),
            'TrangThai' => fake()->randomElement(['Còn bán', 'Ngừng bán']),
        ];
    }

    public function available(): static
    {
        return $this->state(fn (array $attributes) => [
            'TrangThai' => 'Còn bán',
        ]);
    }

    public function outOfStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'TrangThai' => 'Ngừng bán',
        ]);
    }
}
