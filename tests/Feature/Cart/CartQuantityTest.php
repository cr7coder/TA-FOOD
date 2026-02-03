<?php

namespace Tests\Feature\Cart;

use Tests\TestCase;
use App\Models\User;
use App\Models\MonAn;
use App\Models\NhaHang;
use App\Models\GioHang;
use App\Models\GioHangChiTiet;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CartQuantityTest extends TestCase
{
    use RefreshDatabase;

    protected $customer;
    protected $food;
    protected $gioHang;
    protected $cartItem;

    protected function setUp(): void
    {
        parent::setUp();

        $this->customer = User::factory()->create(['VaiTro' => 'KhachHang']);
        $restaurant = NhaHang::factory()->create();
        $this->food = MonAn::factory()->create([
            'MaNhaHang' => $restaurant->MaNhaHang,
            'TrangThai' => 'Còn bán'
        ]);

        // Create cart for user
        $this->gioHang = GioHang::create([
            'MaNguoiDung' => $this->customer->MaNguoiDung,
        ]);

        // Add food to cart
        $this->cartItem = GioHangChiTiet::create([
            'MaGioHang' => $this->gioHang->MaGioHang,
            'MaMonAn' => $this->food->MaMonAn,
            'SoLuong' => 2
        ]);
    }

    /** @test 7E.1 - Món ăn không có trong giỏ hàng */
    public function test_7e1_food_not_in_cart_returns_error()
    {
        $this->actingAs($this->customer, 'sanctum');

        // Sử dụng một MaChiTiet không tồn tại
        $fakeId = 99999;

        $response = $this->putJson('/api/v1/cart/' . $fakeId, [
            'SoLuong' => 3
        ]);

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Món ăn không có trong giỏ hàng (7E.1)'
            ]);
    }

    /** @test 7E.2 - Số lượng không hợp lệ */
    public function test_7e2_invalid_quantity_returns_error()
    {
        $this->actingAs($this->customer, 'sanctum');

        $response = $this->putJson('/api/v1/cart/' . $this->cartItem->MaChiTiet, [
            'SoLuong' => 0
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['SoLuong'])
            ->assertJsonFragment(['Số lượng không hợp lệ (7E.2)']);
    }

    /** @test 7E.3 - Số lượng vượt quá giới hạn */
    public function test_7e3_quantity_exceeds_limit_returns_error()
    {
        $this->actingAs($this->customer, 'sanctum');

        $response = $this->putJson('/api/v1/cart/' . $this->cartItem->MaChiTiet, [
            'SoLuong' => 101 // Giới hạn là 100
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['SoLuong'])
            ->assertJsonFragment(['Số lượng vượt quá giới hạn (7E.3)']);
    }

    /** @test 7E.4 - Món ăn đã hết hàng */
    public function test_7e4_food_out_of_stock_returns_error()
    {
        $this->actingAs($this->customer, 'sanctum');

        $this->food->update(['TrangThai' => 'Ngừng bán']);

        $response = $this->putJson('/api/v1/cart/' . $this->cartItem->MaChiTiet, [
            'SoLuong' => 3
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Món ăn đã hết hàng (7E.4)'
            ]);
    }

    /** @test 7S.1 - Cập nhật số lượng thành công */
    public function test_7s1_update_cart_quantity_success_returns_success_message()
    {
        $this->actingAs($this->customer, 'sanctum');

        $response = $this->putJson('/api/v1/cart/' . $this->cartItem->MaChiTiet, [
            'SoLuong' => 5
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Cập nhật số lượng thành công!'
            ]);

        $this->assertDatabaseHas('gio_hang_chi_tiet', [
            'MaChiTiet' => $this->cartItem->MaChiTiet,
            'MaGioHang' => $this->gioHang->MaGioHang,
            'MaMonAn' => $this->food->MaMonAn,
            'SoLuong' => 5
        ]);
    }
}