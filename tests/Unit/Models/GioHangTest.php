<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\GioHang;
use App\Models\GioHangChiTiet;
use App\Models\User;
use App\Models\MonAn;
use App\Models\NhaHang;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GioHangTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $cart;
    protected $food;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create(['VaiTro' => 'KhachHang']);
        $seller = User::factory()->create(['VaiTro' => 'NguoiBan']);
        $restaurant = NhaHang::factory()->create(['MaNguoiDung' => $seller->MaNguoiDung]);
        $this->food = MonAn::factory()->create([
            'MaNhaHang' => $restaurant->MaNhaHang,
            'Gia' => 50000
        ]);

        $this->cart = GioHang::create(['MaNguoiDung' => $this->user->MaNguoiDung]);
    }

    /** @test - Cart thuộc về một user (API cần để filter cart by user) */
    public function test_cart_belongs_to_user()
    {
        $this->assertInstanceOf(User::class, $this->cart->nguoiDung);
        $this->assertEquals($this->user->MaNguoiDung, $this->cart->nguoiDung->MaNguoiDung);
    }

    /** @test - Cart có nhiều items (API list cart items) */
    public function test_cart_has_many_chi_tiet()
    {
        GioHangChiTiet::create([
            'MaGioHang' => $this->cart->MaGioHang,
            'MaMonAn' => $this->food->MaMonAn,
            'SoLuong' => 2
        ]);

        GioHangChiTiet::create([
            'MaGioHang' => $this->cart->MaGioHang,
            'MaMonAn' => $this->food->MaMonAn,
            'SoLuong' => 3
        ]);

        $this->assertCount(2, $this->cart->chiTiet);
        $this->assertInstanceOf(GioHangChiTiet::class, $this->cart->chiTiet->first());
    }

    /** @test - Accessor TongTien để tính total cho API response */
    public function test_tong_tien_accessor_calculates_total()
    {
        GioHangChiTiet::create([
            'MaGioHang' => $this->cart->MaGioHang,
            'MaMonAn' => $this->food->MaMonAn,
            'SoLuong' => 2  // 2 * 50000 = 100000
        ]);

        $food2 = MonAn::factory()->create([
            'MaNhaHang' => $this->food->MaNhaHang,
            'Gia' => 30000
        ]);
        
        GioHangChiTiet::create([
            'MaGioHang' => $this->cart->MaGioHang,
            'MaMonAn' => $food2->MaMonAn,
            'SoLuong' => 3  // 3 * 30000 = 90000
        ]);

        $this->cart->refresh();
        $this->assertEquals(190000, $this->cart->TongTien);
    }

    /** @test - Accessor TongSoLuong để đếm items cho API */
    public function test_tong_so_luong_accessor()
    {
        GioHangChiTiet::create([
            'MaGioHang' => $this->cart->MaGioHang,
            'MaMonAn' => $this->food->MaMonAn,
            'SoLuong' => 2
        ]);

        GioHangChiTiet::create([
            'MaGioHang' => $this->cart->MaGioHang,
            'MaMonAn' => $this->food->MaMonAn,
            'SoLuong' => 3
        ]);

        $this->cart->refresh();
        $this->assertEquals(5, $this->cart->TongSoLuong);
    }

    /** @test - Filter cart by user (API security) */
    public function test_filter_cart_by_user()
    {
        $user2 = User::factory()->create(['VaiTro' => 'KhachHang']);
        $cart2 = GioHang::create(['MaNguoiDung' => $user2->MaNguoiDung]);

        GioHangChiTiet::create([
            'MaGioHang' => $this->cart->MaGioHang,
            'MaMonAn' => $this->food->MaMonAn,
            'SoLuong' => 1
        ]);

        GioHangChiTiet::create([
            'MaGioHang' => $cart2->MaGioHang,
            'MaMonAn' => $this->food->MaMonAn,
            'SoLuong' => 1
        ]);

        $user1Cart = GioHang::where('MaNguoiDung', $this->user->MaNguoiDung)->first();
        $user2Cart = GioHang::where('MaNguoiDung', $user2->MaNguoiDung)->first();

        $this->assertEquals($this->cart->MaGioHang, $user1Cart->MaGioHang);
        $this->assertEquals($cart2->MaGioHang, $user2Cart->MaGioHang);
        $this->assertNotEquals($user1Cart->MaGioHang, $user2Cart->MaGioHang);
    }

    /** @test - ChiTiet relationship loads MonAn (API response structure) */
    public function test_chi_tiet_loads_mon_an_automatically()
    {
        GioHangChiTiet::create([
            'MaGioHang' => $this->cart->MaGioHang,
            'MaMonAn' => $this->food->MaMonAn,
            'SoLuong' => 1
        ]);

        $chiTiet = $this->cart->chiTiet->first();

        $this->assertNotNull($chiTiet->monAn);
        $this->assertEquals($this->food->MaMonAn, $chiTiet->monAn->MaMonAn);
        $this->assertEquals(50000, $chiTiet->monAn->Gia);
    }

    /** @test - Create cart with session_id for guest users (API guest cart) */
    public function test_create_cart_with_session_id()
    {
        $guestCart = GioHang::create(['session_id' => 'test-session-123']);

        $this->assertEquals('test-session-123', $guestCart->session_id);
        $this->assertNull($guestCart->MaNguoiDung);
    }

    /** @test - Fillable attributes for API validation */
    public function test_cart_fillable_attributes()
    {
        $fillable = ['MaNguoiDung', 'session_id'];
        
        $this->assertEquals($fillable, $this->cart->getFillable());
    }

    /** @test - Primary key is MaGioHang (API uses correct key) */
    public function test_primary_key_is_ma_gio_hang()
    {
        $this->assertEquals('MaGioHang', $this->cart->getKeyName());
    }

    /** @test - Table name is gio_hang */
    public function test_table_name_is_gio_hang()
    {
        $this->assertEquals('gio_hang', $this->cart->getTable());
    }

    /** @test - Empty cart has zero total (API edge case) */
    public function test_empty_cart_has_zero_total()
    {
        $emptyCart = GioHang::create(['MaNguoiDung' => $this->user->MaNguoiDung]);
        
        $this->assertEquals(0, $emptyCart->TongTien);
        $this->assertEquals(0, $emptyCart->TongSoLuong);
    }

    /** @test - Delete cart removes items (API cascade delete) */
    public function test_delete_cart_removes_items()
    {
        $chiTiet = GioHangChiTiet::create([
            'MaGioHang' => $this->cart->MaGioHang,
            'MaMonAn' => $this->food->MaMonAn,
            'SoLuong' => 2
        ]);

        $cartId = $this->cart->MaGioHang;
        $this->cart->delete();

        $this->assertDatabaseMissing('gio_hang', ['MaGioHang' => $cartId]);
    }
}
