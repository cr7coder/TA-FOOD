<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\MonAn;
use App\Models\NhaHang;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MonAnTest extends TestCase
{
    use RefreshDatabase;

    protected $restaurant;
    protected $food;

    protected function setUp(): void
    {
        parent::setUp();

        $seller = User::factory()->create(['VaiTro' => 'NguoiBan']);
        $this->restaurant = NhaHang::factory()->create(['MaNguoiDung' => $seller->MaNguoiDung]);
        $this->food = MonAn::factory()->create([
            'MaNhaHang' => $this->restaurant->MaNhaHang,
            'TenMonAn' => 'Cơm Gà',
            'DanhMuc' => 'Cơm',
            'Gia' => 45000,
            'MoTa' => 'Cơm gà thơm ngon',
            'TrangThai' => 'Còn bán'
        ]);
    }

    /** @test - Fillable attributes for API validation */
    public function test_food_has_correct_fillable_attributes()
    {
        $this->assertEquals('Cơm Gà', $this->food->TenMonAn);
        $this->assertEquals('Cơm', $this->food->DanhMuc);
        $this->assertEquals(45000, $this->food->Gia);
        $this->assertEquals('Cơm gà thơm ngon', $this->food->MoTa);
    }

    /** @test - Food thuộc về restaurant (API filter by restaurant) */
    public function test_food_belongs_to_restaurant()
    {
        $this->assertInstanceOf(NhaHang::class, $this->food->nhaHang);
        $this->assertEquals($this->restaurant->MaNhaHang, $this->food->nhaHang->MaNhaHang);
    }

    /** @test - Filter foods by status (API available foods) */
    public function test_filter_foods_by_status()
    {
        MonAn::factory()->count(2)->create([
            'MaNhaHang' => $this->restaurant->MaNhaHang,
            'TrangThai' => 'Còn bán'
        ]);
        
        MonAn::factory()->count(3)->create([
            'MaNhaHang' => $this->restaurant->MaNhaHang,
            'TrangThai' => 'Ngừng bán'
        ]);

        $available = MonAn::where('TrangThai', 'Còn bán')->count();
        $unavailable = MonAn::where('TrangThai', 'Ngừng bán')->count();

        $this->assertEquals(3, $available);  // +1 from setUp
        $this->assertEquals(3, $unavailable);
    }

    /** @test - Price formatting for API response */
    public function test_price_is_numeric()
    {
        $this->assertIsNumeric($this->food->Gia);
        $this->assertEquals(45000, $this->food->Gia);
    }

    /** @test - Query foods by restaurant (API filter) */
    public function test_query_foods_by_restaurant()
    {
        MonAn::factory()->count(3)->create([
            'MaNhaHang' => $this->restaurant->MaNhaHang
        ]);

        $foods = MonAn::where('MaNhaHang', $this->restaurant->MaNhaHang)->get();

        $this->assertCount(4, $foods);  // +1 from setUp
    }

    /** @test - Query foods by category (API filter) */
    public function test_query_foods_by_category()
    {
        MonAn::factory()->create([
            'MaNhaHang' => $this->restaurant->MaNhaHang,
            'DanhMuc' => 'Cơm'
        ]);
        
        MonAn::factory()->create([
            'MaNhaHang' => $this->restaurant->MaNhaHang,
            'DanhMuc' => 'Bún'
        ]);

        $comFoods = MonAn::where('DanhMuc', 'Cơm')->count();

        $this->assertEquals(2, $comFoods);  // +1 from setUp
    }

    /** @test - Primary key is MaMonAn (API uses correct key) */
    public function test_primary_key_is_ma_mon_an()
    {
        $this->assertEquals('MaMonAn', $this->food->getKeyName());
    }

    /** @test - Table name is mon_an */
    public function test_table_name_is_mon_an()
    {
        $this->assertEquals('mon_an', $this->food->getTable());
    }

    /** @test - Create food with valid data (API create endpoint) */
    public function test_create_food_with_valid_data()
    {
        $newFood = MonAn::create([
            'MaNhaHang' => $this->restaurant->MaNhaHang,
            'TenMonAn' => 'Phở Bò',
            'DanhMuc' => 'Phở',
            'Gia' => 50000,
            'MoTa' => 'Phở bò truyền thống',
            'TrangThai' => 'Còn bán'
        ]);

        $this->assertDatabaseHas('mon_an', [
            'TenMonAn' => 'Phở Bò',
            'MaNhaHang' => $this->restaurant->MaNhaHang
        ]);
    }

    /** @test - Update food status (API toggle availability) */
    public function test_update_food_status()
    {
        $this->assertEquals('Còn bán', $this->food->TrangThai);

        $this->food->update(['TrangThai' => 'Ngừng bán']);

        $this->assertEquals('Ngừng bán', $this->food->fresh()->TrangThai);
    }

    /** @test - Delete food (API delete endpoint) */
    public function test_delete_food()
    {
        $foodId = $this->food->MaMonAn;
        $this->food->delete();

        $this->assertDatabaseMissing('mon_an', ['MaMonAn' => $foodId]);
    }
}
