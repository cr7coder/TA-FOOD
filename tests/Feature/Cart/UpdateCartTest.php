<?php

namespace Tests\Feature\Cart;

use Tests\TestCase;
use App\Models\User;
use App\Models\MonAn;
use App\Models\NhaHang;
use App\Models\GioHang;
use App\Models\GioHangChiTiet;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UpdateCartTest extends TestCase
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
            'TrangThai' => 'Còn bán',
            'Gia' => 50000
        ]);

        // Tạo giỏ hàng và thêm món
        $this->gioHang = GioHang::create([
            'MaNguoiDung' => $this->customer->MaNguoiDung,
        ]);

        $this->cartItem = GioHangChiTiet::create([
            'MaGioHang' => $this->gioHang->MaGioHang,
            'MaMonAn' => $this->food->MaMonAn,
            'SoLuong' => 2
        ]);
    }

    /** @test - Cập nhật số lượng thành công */
    public function test_update_cart_quantity_success()
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
            'SoLuong' => 5
        ]);
    }

    /** @test - Số lượng không hợp lệ (0) */
    public function test_update_cart_with_zero_quantity_returns_error()
    {
        $this->actingAs($this->customer, 'sanctum');

        $response = $this->putJson('/api/v1/cart/' . $this->cartItem->MaChiTiet, [
            'SoLuong' => 0
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['SoLuong']);
    }

    /** @test - Số lượng vượt quá giới hạn */
    public function test_update_cart_quantity_exceeds_limit_returns_error()
    {
        $this->actingAs($this->customer, 'sanctum');

        $response = $this->putJson('/api/v1/cart/' . $this->cartItem->MaChiTiet, [
            'SoLuong' => 101
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['SoLuong']);
    }

    /** @test - Món không có trong giỏ hàng */
    public function test_update_non_existent_cart_item_returns_error()
    {
        $this->actingAs($this->customer, 'sanctum');

        $response = $this->putJson('/api/v1/cart/99999', [
            'SoLuong' => 3
        ]);

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Món ăn không có trong giỏ hàng (7E.1)'
            ]);
    }

    /** @test - Món đã hết hàng */
    public function test_update_cart_item_when_food_out_of_stock_returns_error()
    {
        $this->actingAs($this->customer, 'sanctum');

        // Đổi trạng thái món thành hết hàng
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

    /** @test - Cập nhật giỏ của user khác bị lỗi */
    public function test_update_other_user_cart_item_returns_error()
    {
        // Tạo user khác
        $otherUser = User::factory()->create(['VaiTro' => 'KhachHang']);
        
        // Login bằng user khác
        $this->actingAs($otherUser, 'sanctum');

        // Cố gắng cập nhật giỏ của customer
        $response = $this->putJson('/api/v1/cart/' . $this->cartItem->MaChiTiet, [
            'SoLuong' => 10
        ]);

        // Không tìm thấy vì không thuộc giỏ của otherUser
        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Món ăn không có trong giỏ hàng (7E.1)'
            ]);

        // Số lượng cũ vẫn giữ nguyên
        $this->assertDatabaseHas('gio_hang_chi_tiet', [
            'MaChiTiet' => $this->cartItem->MaChiTiet,
            'SoLuong' => 2
        ]);
    }

    /** @test - Thiếu trường SoLuong */
    public function test_update_cart_without_quantity_returns_error()
    {
        $this->actingAs($this->customer, 'sanctum');

        $response = $this->putJson('/api/v1/cart/' . $this->cartItem->MaChiTiet, []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['SoLuong']);
    }

    /** @test - SoLuong không phải số nguyên */
    public function test_update_cart_with_non_integer_quantity_returns_error()
    {
        $this->actingAs($this->customer, 'sanctum');

        $response = $this->putJson('/api/v1/cart/' . $this->cartItem->MaChiTiet, [
            'SoLuong' => 'abc'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['SoLuong']);
    }

    /** @test - Cập nhật về số lượng tối thiểu (1) */
    public function test_update_cart_to_minimum_quantity()
    {
        $this->actingAs($this->customer, 'sanctum');

        $response = $this->putJson('/api/v1/cart/' . $this->cartItem->MaChiTiet, [
            'SoLuong' => 1
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Cập nhật số lượng thành công!'
            ]);

        $this->assertDatabaseHas('gio_hang_chi_tiet', [
            'MaChiTiet' => $this->cartItem->MaChiTiet,
            'SoLuong' => 1
        ]);
    }

    /** @test - Cập nhật lên số lượng tối đa (100) */
    public function test_update_cart_to_maximum_quantity()
    {
        $this->actingAs($this->customer, 'sanctum');

        $response = $this->putJson('/api/v1/cart/' . $this->cartItem->MaChiTiet, [
            'SoLuong' => 100
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Cập nhật số lượng thành công!'
            ]);

        $this->assertDatabaseHas('gio_hang_chi_tiet', [
            'MaChiTiet' => $this->cartItem->MaChiTiet,
            'SoLuong' => 100
        ]);
    }
}
