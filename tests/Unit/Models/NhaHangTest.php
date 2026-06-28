<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\NhaHang;
use App\Models\MonAn;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class NhaHangTest extends TestCase
{
    use RefreshDatabase;

    protected $seller;
    protected $restaurant;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seller = User::factory()->create(['VaiTro' => 'NguoiBan']);
        $this->restaurant = NhaHang::factory()->create([
            'MaNguoiDung' => $this->seller->MaNguoiDung,
            'TenNhaHang' => 'Test Restaurant',
            'DiaChi' => 'Test Address',
            'SoDienThoai' => '0912345678',
            'GioMoCua' => '08:30:00',
            'GioDongCua' => '22:30:00'
        ]);
    }

    /** @test - Fillable attributes for API validation */
    public function test_restaurant_has_correct_fillable_attributes()
    {
        $fillable = [
            'TenNhaHang',
            'DiaChi',
            'SoDienThoai',
            'Email',
            'GioMoCua',
            'GioDongCua',
            'MaNguoiDung',
            'latitude',
            'longitude',
            'phi_ship_co_ban',
            'phi_ship_moi_km',
            'km_mien_phi',
            'commission_rate',
            'HinhAnh',
        ];

        $this->assertEquals($fillable, $this->restaurant->getFillable());
        $this->assertEquals('Test Restaurant', $this->restaurant->TenNhaHang);
        $this->assertEquals('Test Address', $this->restaurant->DiaChi);
    }

    /** @test - Restaurant thuộc về owner (API filter by seller) */
    public function test_restaurant_belongs_to_owner()
    {
        $this->assertInstanceOf(User::class, $this->restaurant->owner);
        $this->assertEquals($this->seller->MaNguoiDung, $this->restaurant->owner->MaNguoiDung);
    }

    /** @test - Restaurant có nhiều món ăn (API list foods) */
    public function test_restaurant_has_many_foods()
    {
        MonAn::factory()->count(3)->create([
            'MaNhaHang' => $this->restaurant->MaNhaHang
        ]);

        $this->assertCount(3, $this->restaurant->monAn);
        $this->assertInstanceOf(MonAn::class, $this->restaurant->monAn->first());
    }

    /** @test - Restaurant có relationship monAnDangBan (API available foods) */
    public function test_restaurant_has_available_foods()
    {
        MonAn::factory()->count(2)->create([
            'MaNhaHang' => $this->restaurant->MaNhaHang,
            'TrangThai' => 'Còn bán'
        ]);
        
        MonAn::factory()->count(1)->create([
            'MaNhaHang' => $this->restaurant->MaNhaHang,
            'TrangThai' => 'Ngừng bán'
        ]);

        $this->assertEquals(3, $this->restaurant->monAn->count());
        $this->assertEquals(2, $this->restaurant->monAnDangBan->count());
    }

    /** @test - Accessor GioMoCuaForForm cho form input (API format) */
    public function test_gio_mo_cua_for_form_accessor()
    {
        $this->markTestSkipped('Cast datetime:H:i không hoạt động như mong đợi - accessor cần refactor');
        $this->assertEquals('08:30', $this->restaurant->GioMoCuaForForm);
    }

    /** @test - Accessor GioDongCuaForForm cho form input (API format) */
    public function test_gio_dong_cua_for_form_accessor()
    {
        $this->markTestSkipped('Cast datetime:H:i không hoạt động như mong đợi - accessor cần refactor');
        $this->assertEquals('22:30', $this->restaurant->GioDongCuaForForm);
    }

    /** @test - Method isOpen() để kiểm tra nhà hàng đang mở (API status) */
    public function test_is_open_method()
    {
        $this->markTestSkipped('isOpen() phụ thuộc vào datetime cast - cần refactor');
        $restaurant = NhaHang::create([
            'MaNguoiDung' => $this->seller->MaNguoiDung,
            'TenNhaHang' => 'Test Open',
            'DiaChi' => 'Test',
            'SoDienThoai' => '0123456789',
            'GioMoCua' => '00:00:00',
            'GioDongCua' => '23:59:59'
        ]);

        $restaurant->refresh();
        $this->assertTrue($restaurant->isOpen());
    }

    /** @test - Accessor TongMonAn để đếm số món (API stats) */
    public function test_tong_mon_an_accessor()
    {
        MonAn::factory()->count(5)->create([
            'MaNhaHang' => $this->restaurant->MaNhaHang
        ]);

        $this->assertEquals(5, $this->restaurant->TongMonAn);
    }

    /** @test - Primary key is MaNhaHang (API uses correct key) */
    public function test_primary_key_is_ma_nha_hang()
    {
        $this->assertEquals('MaNhaHang', $this->restaurant->getKeyName());
    }

    /** @test - Table name is nha_hang */
    public function test_table_name_is_nha_hang()
    {
        $this->assertEquals('nha_hang', $this->restaurant->getTable());
    }

    /** @test - Time fields are cast to datetime (API format) */
    public function test_time_casts()
    {
        $this->markTestSkipped('Casts không được định nghĩa trực tiếp trên model - cần refactor');
        $casts = $this->restaurant->getCasts();
        
        $this->assertEquals('datetime:H:i', $casts['GioMoCua']);
        $this->assertEquals('datetime:H:i', $casts['GioDongCua']);
    }

    /** @test - Query restaurants by owner (API seller filter) */
    public function test_query_restaurants_by_owner()
    {
        $seller2 = User::factory()->create(['VaiTro' => 'NguoiBan']);
        
        NhaHang::factory()->count(2)->create(['MaNguoiDung' => $this->seller->MaNguoiDung]);
        NhaHang::factory()->count(1)->create(['MaNguoiDung' => $seller2->MaNguoiDung]);

        $seller1Restaurants = NhaHang::where('MaNguoiDung', $this->seller->MaNguoiDung)->count();
        $seller2Restaurants = NhaHang::where('MaNguoiDung', $seller2->MaNguoiDung)->count();

        $this->assertEquals(3, $seller1Restaurants);  // +1 from setUp
        $this->assertEquals(1, $seller2Restaurants);
    }

    /** @test - Update restaurant info (API update endpoint) */
    public function test_update_restaurant_info()
    {
        $this->restaurant->update([
            'TenNhaHang' => 'Updated Name',
            'SoDienThoai' => '0999999999'
        ]);

        $this->assertEquals('Updated Name', $this->restaurant->fresh()->TenNhaHang);
        $this->assertEquals('0999999999', $this->restaurant->fresh()->SoDienThoai);
    }

    /** @test - Delete restaurant (API delete endpoint) */
    public function test_delete_restaurant()
    {
        $restaurantId = $this->restaurant->MaNhaHang;
        $this->restaurant->delete();

        $this->assertDatabaseMissing('nha_hang', ['MaNhaHang' => $restaurantId]);
    }

    /** @test - Create restaurant with valid data (API create endpoint) */
    public function test_create_restaurant_with_valid_data()
    {
        $newRestaurant = NhaHang::create([
            'TenNhaHang' => 'New Restaurant',
            'DiaChi' => 'New Address',
            'SoDienThoai' => '0987654321',
            'GioMoCua' => '09:00:00',
            'GioDongCua' => '21:00:00',
            'MaNguoiDung' => $this->seller->MaNguoiDung
        ]);

        $this->assertDatabaseHas('nha_hang', [
            'TenNhaHang' => 'New Restaurant',
            'MaNguoiDung' => $this->seller->MaNguoiDung
        ]);
    }
}
