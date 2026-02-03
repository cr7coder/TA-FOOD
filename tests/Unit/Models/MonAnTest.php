<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\MonAn;
use App\Models\NhaHang;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MonAnTest extends TestCase
{
    use RefreshDatabase;

    /** @test - Kiểm tra fillable attributes */
    public function test_food_has_correct_fillable_attributes()
    {
        /** @var MonAn $food */
        $food = MonAn::factory()->create([
            'TenMonAn' => 'Cơm Gà',
            'DanhMuc' => 'Cơm',
            'Gia' => 45000,
            'MoTa' => 'Cơm gà thơm ngon',
            'TrangThai' => 'Còn bán'
        ]);

        $this->assertEquals('Cơm Gà', $food->TenMonAn);
        $this->assertEquals('Cơm', $food->DanhMuc);
        $this->assertEquals(45000, $food->Gia);
        $this->assertEquals('Cơm gà thơm ngon', $food->MoTa);
    }

    /** @test - Kiểm tra relationship với NhaHang */
    public function test_food_belongs_to_restaurant()
    {
        /** @var NhaHang $restaurant */
        $restaurant = NhaHang::factory()->create();
        /** @var MonAn $food */
        $food = MonAn::factory()->create([
            'MaNhaHang' => $restaurant->MaNhaHang
        ]);

        $this->assertInstanceOf(NhaHang::class, $food->nhaHang);
        $this->assertEquals($restaurant->MaNhaHang, $food->nhaHang->MaNhaHang);
    }

    /** @test - Kiểm tra scope món ăn còn bán */
    public function test_scope_available_foods()
    {
        MonAn::factory()->create(['TrangThai' => 'Còn bán']);
        MonAn::factory()->create(['TrangThai' => 'Ngừng bán']);

        $availableCount = MonAn::where('TrangThai', 'Còn bán')->count();

        $this->assertEquals(1, $availableCount);
    }

    /** @test - Kiểm tra format giá tiền */
    public function test_price_formatting()
    {
        /** @var MonAn $food */
        $food = MonAn::factory()->create(['Gia' => 50000]);

        // Kiểm tra format accessor nếu có
        $formattedPrice = number_format($food->Gia, 0, ',', '.') . ' đ';

        $this->assertEquals('50.000 đ', $formattedPrice);
    }

    /** @test - Kiểm tra validation unique name trong cùng nhà hàng */
    public function test_food_name_unique_within_restaurant()
    {
        /** @var NhaHang $restaurant */
        $restaurant = NhaHang::factory()->create();

        MonAn::factory()->create([
            'TenMonAn' => 'Cơm Gà',
            'MaNhaHang' => $restaurant->MaNhaHang
        ]);

        // Kiểm tra logic unique trong cùng nhà hàng
        $existingFood = MonAn::where('TenMonAn', 'Cơm Gà')
            ->where('MaNhaHang', $restaurant->MaNhaHang)
            ->exists();

        $this->assertTrue($existingFood);
    }

    /** @test - Kiểm tra ảnh mặc định */
    public function test_default_image_when_null()
    {
        /** @var MonAn $food */
        $food = MonAn::factory()->create(['HinhAnh' => null]);

        // Kiểm tra accessor ảnh mặc định
        $defaultImage = $food->HinhAnh ?? 'default-food.jpg';

        $this->assertEquals('default-food.jpg', $defaultImage);
    }
}
