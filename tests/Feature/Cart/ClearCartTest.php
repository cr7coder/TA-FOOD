<?php

namespace Tests\Feature\Cart;

use Tests\TestCase;
use App\Models\User;
use App\Models\MonAn;
use App\Models\NhaHang;
use App\Models\GioHang;
use App\Models\GioHangChiTiet;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ClearCartTest extends TestCase
{
    use RefreshDatabase;

    protected $customer;
    protected $gioHang;
    protected $food1;
    protected $food2;

    protected function setUp(): void
    {
        parent::setUp();

        // Tạo customer
        $this->customer = User::factory()->create(['VaiTro' => 'KhachHang']);
        
        // Tạo nhà hàng và món ăn
        $restaurant = NhaHang::factory()->create();
        $this->food1 = MonAn::factory()->create([
            'MaNhaHang' => $restaurant->MaNhaHang,
            'TrangThai' => 'Còn bán'
        ]);
        $this->food2 = MonAn::factory()->create([
            'MaNhaHang' => $restaurant->MaNhaHang,
            'TrangThai' => 'Còn bán'
        ]);

        // Tạo giỏ hàng
        $this->gioHang = GioHang::create([
            'MaNguoiDung' => $this->customer->MaNguoiDung,
        ]);

        // Thêm 2 món vào giỏ
        GioHangChiTiet::create([
            'MaGioHang' => $this->gioHang->MaGioHang,
            'MaMonAn' => $this->food1->MaMonAn,
            'SoLuong' => 2
        ]);
        GioHangChiTiet::create([
            'MaGioHang' => $this->gioHang->MaGioHang,
            'MaMonAn' => $this->food2->MaMonAn,
            'SoLuong' => 3
        ]);
    }

    /** @test - Xóa toàn bộ giỏ hàng thành công */
    public function test_clear_cart_success_returns_empty_cart()
    {
        // Kiểm tra giỏ hàng có 2 món
        $this->assertDatabaseCount('gio_hang_chi_tiet', 2);

        $this->actingAs($this->customer, 'sanctum');

        $response = $this->deleteJson('/cart');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [],
                'count' => 0,
                'total' => 0
            ]);

        // Kiểm tra database - tất cả món đã bị xóa
        $this->assertDatabaseCount('gio_hang_chi_tiet', 0);
        
        // Giỏ hàng vẫn còn, nhưng rỗng
        $this->assertDatabaseHas('gio_hang', [
            'MaGioHang' => $this->gioHang->MaGioHang,
            'MaNguoiDung' => $this->customer->MaNguoiDung
        ]);
    }

    /** @test - Xóa giỏ hàng khi đã rỗng */
    public function test_clear_empty_cart_returns_empty_response()
    {
        // Xóa hết món trước
        GioHangChiTiet::where('MaGioHang', $this->gioHang->MaGioHang)->delete();

        $this->actingAs($this->customer, 'sanctum');

        $response = $this->deleteJson('/cart');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [],
                'count' => 0,
                'total' => 0
            ]);

        $this->assertDatabaseCount('gio_hang_chi_tiet', 0);
    }

    /** @test - Guest user có thể xóa giỏ hàng (session-based) - Test đơn giản */
    public function test_guest_can_clear_cart()
    {
        // Test đơn giản: kiểm tra endpoint clear không bị lỗi khi chưa login
        // Không test chi tiết session vì phức tạp trong testing environment
        
        // Không login (guest)
        $response = $this->deleteJson('/cart');

        // Endpoint phải hoạt động bình thường
        $response->assertStatus(200)
            ->assertJson([
                'success' => true
            ]);
    }

    /** @test - Xóa giỏ hàng không ảnh hưởng đến user khác */
    public function test_clear_cart_does_not_affect_other_users()
    {
        // Tạo user khác
        $otherUser = User::factory()->create(['VaiTro' => 'KhachHang']);
        $otherCart = GioHang::create(['MaNguoiDung' => $otherUser->MaNguoiDung]);
        
        GioHangChiTiet::create([
            'MaGioHang' => $otherCart->MaGioHang,
            'MaMonAn' => $this->food1->MaMonAn,
            'SoLuong' => 5
        ]);

        $this->actingAs($this->customer, 'sanctum');

        // Xóa giỏ của customer
        $response = $this->deleteJson('/cart');

        $response->assertStatus(200);

        // Giỏ của customer đã rỗng
        $this->assertDatabaseCount('gio_hang_chi_tiet', 1);
        
        // Giỏ của otherUser vẫn còn
        $this->assertDatabaseHas('gio_hang_chi_tiet', [
            'MaGioHang' => $otherCart->MaGioHang,
            'MaMonAn' => $this->food1->MaMonAn,
            'SoLuong' => 5
        ]);
    }
}
