<?php

namespace Tests\Feature\Checkout;

use Tests\TestCase;
use App\Models\User;
use App\Models\MonAn;
use App\Models\NhaHang;
use App\Models\GioHang;
use App\Models\GioHangChiTiet;
use App\Models\GiamGia;
use App\Models\DonHang;
use App\Models\ThanhToan;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CheckoutProcessTest extends TestCase
{
    use RefreshDatabase;

    protected $customer;
    protected $gioHang;
    protected $food;
    protected $voucher;

    protected function setUp(): void
    {
        parent::setUp();

        $this->customer = User::factory()->create(['VaiTro' => 'KhachHang']);
        $restaurant = NhaHang::factory()->create();
        $this->food = MonAn::factory()->create([
            'MaNhaHang' => $restaurant->MaNhaHang,
            'TrangThai' => 'Còn bán',
            'Gia' => 100000
        ]);

        // Tạo giỏ hàng với món
        $this->gioHang = GioHang::create([
            'MaNguoiDung' => $this->customer->MaNguoiDung,
        ]);

        GioHangChiTiet::create([
            'MaGioHang' => $this->gioHang->MaGioHang,
            'MaMonAn' => $this->food->MaMonAn,
            'SoLuong' => 2
        ]);

        // Tạo voucher
        $this->voucher = GiamGia::create([
            'MaCode' => 'DISCOUNT10',
            'TenGiamGia' => 'Giảm 10%',
            'PhanTram' => 10,
            'DonHangToiThieu' => 100000,
            'NgayBatDau' => now()->subDay(),
            'NgayKetThuc' => now()->addWeek(),
            'SoLuong' => 10
        ]);
    }

    /** @test - Lấy thông tin checkout thành công */
    public function test_get_checkout_info_success()
    {
        $this->actingAs($this->customer, 'sanctum');

        $response = $this->getJson('/api/v1/checkout');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true
            ])
            ->assertJsonStructure([
                'success',
                'data' => [
                    'cart_items',
                    'subtotal',
                    'vouchers',
                    'user_info' => ['name', 'phone', 'email']
                ]
            ]);
    }

    /** @test - Giỏ hàng trống khi checkout */
    public function test_get_checkout_info_with_empty_cart_returns_error()
    {
        $this->actingAs($this->customer, 'sanctum');

        // Xóa hết món trong giỏ
        $this->gioHang->chiTiet()->delete();

        $response = $this->getJson('/api/v1/checkout');

        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
                'message' => 'Giỏ hàng trống'
            ]);
    }

    /** @test - Áp dụng voucher thành công */
    public function test_apply_voucher_success()
    {
        $this->actingAs($this->customer, 'sanctum');

        $response = $this->postJson('/api/v1/checkout/apply-voucher', [
            'voucher_code' => 'DISCOUNT10'
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Áp dụng mã DISCOUNT10 (-10%) thành công'
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'voucher_code',
                    'voucher_id',
                    'percent',
                    'discount',
                    'total',
                    'subtotal'
                ]
            ]);

        // Kiểm tra discount = 10% của 200000
        $response->assertJsonPath('data.discount', 20000);
        $response->assertJsonPath('data.total', 180000);
    }

    /** @test - Voucher không hợp lệ */
    public function test_apply_invalid_voucher_returns_error()
    {
        $this->actingAs($this->customer, 'sanctum');

        $response = $this->postJson('/api/v1/checkout/apply-voucher', [
            'voucher_code' => 'INVALID_CODE'
        ]);

        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
                'message' => 'Mã giảm giá không hợp lệ hoặc đã hết hạn'
            ]);
    }

    /** @test - Voucher đã hết hạn */
    public function test_apply_expired_voucher_returns_error()
    {
        $this->actingAs($this->customer, 'sanctum');

        // Tạo voucher hết hạn
        $expiredVoucher = GiamGia::create([
            'MaCode' => 'EXPIRED',
            'TenGiamGia' => 'Đã hết hạn',
            'PhanTram' => 10,
            'NgayBatDau' => now()->subWeek(),
            'NgayKetThuc' => now()->subDay(),
            'SoLuong' => 10
        ]);

        $response = $this->postJson('/api/v1/checkout/apply-voucher', [
            'voucher_code' => 'EXPIRED'
        ]);

        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
                'message' => 'Mã giảm giá không hợp lệ hoặc đã hết hạn'
            ]);
    }

    /** @test - Đơn hàng chưa đủ tối thiểu */
    public function test_apply_voucher_with_insufficient_order_value_returns_error()
    {
        $this->actingAs($this->customer, 'sanctum');

        // Tạo voucher yêu cầu tối thiểu 500k
        $highMinVoucher = GiamGia::create([
            'MaCode' => 'VIP500',
            'TenGiamGia' => 'VIP 500k',
            'PhanTram' => 20,
            'DonHangToiThieu' => 500000,
            'NgayBatDau' => now()->subDay(),
            'NgayKetThuc' => now()->addWeek(),
            'SoLuong' => 10
        ]);

        $response = $this->postJson('/api/v1/checkout/apply-voucher', [
            'voucher_code' => 'VIP500'
        ]);

        $response->assertStatus(400)
            ->assertJsonFragment([
                'success' => false
            ]);
    }

    /** @test - Tạo đơn hàng COD thành công */
    public function test_create_order_with_cod_success()
    {
        $this->actingAs($this->customer, 'sanctum');

        $response = $this->postJson('/api/v1/checkout/create-order', [
            'name' => 'Nguyễn Văn A',
            'phone' => '0123456789',
            'address' => '123 Đường ABC, Q1, TP.HCM',
            'payment_method' => 'COD',
            'note' => 'Giao giờ hành chính'
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Đặt hàng thành công'
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'order_id',
                    'total_amount',
                    'payment_method',
                    'status'
                ]
            ]);

        // Kiểm tra database
        $this->assertDatabaseHas('don_hang', [
            'MaNguoiDung' => $this->customer->MaNguoiDung,
            'TenKhachHang' => 'Nguyễn Văn A',
            'PhuongThucThanhToan' => 'COD',
            'TrangThai' => 'Đã thanh toán'
        ]);

        // Giỏ hàng đã bị xóa
        $this->assertDatabaseMissing('gio_hang', [
            'MaGioHang' => $this->gioHang->MaGioHang
        ]);
    }

    /** @test - Tạo đơn hàng với voucher */
    public function test_create_order_with_voucher_success()
    {
        $this->actingAs($this->customer, 'sanctum');

        $response = $this->postJson('/api/v1/checkout/create-order', [
            'name' => 'Nguyễn Văn B',
            'phone' => '0987654321',
            'address' => '456 Đường XYZ',
            'payment_method' => 'COD',
            'voucher_id' => $this->voucher->MaGiamGia
        ]);

        $response->assertStatus(201);

        // Tổng tiền = 200000 - 10% = 180000
        $response->assertJsonPath('data.total_amount', 180000);

        $this->assertDatabaseHas('don_hang', [
            'MaGiamGia' => $this->voucher->MaGiamGia,
            'TongTien' => 180000
        ]);
    }

    /** @test - Tạo đơn với thanh toán online */
    public function test_create_order_with_online_payment_success()
    {
        $this->actingAs($this->customer, 'sanctum');

        $response = $this->postJson('/api/v1/checkout/create-order', [
            'name' => 'Nguyễn Văn C',
            'phone' => '0123456789',
            'address' => '789 Đường DEF',
            'payment_method' => 'MoMo'
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => ['payment_url']
            ]);

        $this->assertDatabaseHas('don_hang', [
            'PhuongThucThanhToan' => 'MoMo',
            'TrangThai' => 'Chờ xử lý'
        ]);

        $this->assertDatabaseHas('thanh_toan', [
            'PhuongThuc' => 'MoMo',
            'TrangThai' => 'Chờ thanh toán'
        ]);
    }

    /** @test - Giỏ hàng trống khi tạo đơn */
    public function test_create_order_with_empty_cart_returns_error()
    {
        $this->actingAs($this->customer, 'sanctum');

        $this->gioHang->chiTiet()->delete();

        $response = $this->postJson('/api/v1/checkout/create-order', [
            'name' => 'Test User',
            'phone' => '0123456789',
            'address' => 'Test Address',
            'payment_method' => 'COD'
        ]);

        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
                'message' => 'Giỏ hàng trống'
            ]);
    }

    /** @test - Thiếu thông tin bắt buộc */
    public function test_create_order_with_missing_required_fields_returns_error()
    {
        $this->actingAs($this->customer, 'sanctum');

        $response = $this->postJson('/api/v1/checkout/create-order', [
            'name' => 'Test'
            // Thiếu phone, address, payment_method
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['phone', 'address', 'payment_method']);
    }

    /** @test - Phương thức thanh toán không hợp lệ */
    public function test_create_order_with_invalid_payment_method_returns_error()
    {
        $this->actingAs($this->customer, 'sanctum');

        $response = $this->postJson('/api/v1/checkout/create-order', [
            'name' => 'Test User',
            'phone' => '0123456789',
            'address' => 'Test Address',
            'payment_method' => 'INVALID_METHOD'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['payment_method']);
    }
}
