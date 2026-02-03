<?php

namespace Tests\Feature\Checkout;

use Tests\TestCase;
use App\Models\User;
use App\Models\MonAn;
use App\Models\NhaHang;
use App\Models\DonHang;
use App\Models\DonHangChiTiet;
use App\Models\ThanhToan;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    protected $customer;
    protected $donHang;
    protected $thanhToan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->customer = User::factory()->create(['VaiTro' => 'KhachHang']);
        $restaurant = NhaHang::factory()->create();
        $food = MonAn::factory()->create([
            'MaNhaHang' => $restaurant->MaNhaHang,
            'Gia' => 50000
        ]);

        // Tạo đơn hàng
        $this->donHang = DonHang::create([
            'MaNguoiDung' => $this->customer->MaNguoiDung,
            'TongTien' => 150000,
            'PhuongThucThanhToan' => 'MoMo',
            'TrangThai' => 'Chờ xử lý',
            'TenKhachHang' => 'Test Customer',
            'SoDienThoai' => '0123456789',
            'DiaChiGiaoHang' => 'Test Address'
        ]);

        DonHangChiTiet::create([
            'MaDonHang' => $this->donHang->MaDonHang,
            'MaMonAn' => $food->MaMonAn,
            'SoLuong' => 3,
            'Gia' => 50000
        ]);

        // Tạo thanh toán
        $this->thanhToan = ThanhToan::create([
            'MaDonHang' => $this->donHang->MaDonHang,
            'PhuongThuc' => 'MoMo',
            'SoTien' => 150000,
            'TrangThai' => 'Chờ thanh toán'
        ]);
    }

    /** @test - Lấy danh sách thanh toán thành công */
    public function test_get_payment_list_success()
    {
        $this->actingAs($this->customer, 'sanctum');

        $response = $this->getJson('/api/v1/payments');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true
            ])
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'payment_id',
                        'order_id',
                        'amount',
                        'method',
                        'status',
                        'created_at'
                    ]
                ],
                'pagination' => [
                    'current_page',
                    'total_pages',
                    'total_items',
                    'per_page'
                ]
            ]);
    }

    /** @test - Xem chi tiết thanh toán thành công */
    public function test_get_payment_detail_success()
    {
        $this->actingAs($this->customer, 'sanctum');

        $response = $this->getJson('/api/v1/payments/' . $this->thanhToan->MaThanhToan);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'payment_id' => $this->thanhToan->MaThanhToan,
                    'amount' => 150000,
                    'method' => 'MoMo',
                    'status' => 'Chờ thanh toán'
                ]
            ])
            ->assertJsonStructure([
                'success',
                'data' => [
                    'payment_id',
                    'order_id',
                    'amount',
                    'method',
                    'status',
                    'order' => [
                        'customer_name',
                        'phone',
                        'address',
                        'total_amount',
                        'items'
                    ]
                ]
            ]);
    }

    /** @test - Thanh toán không tồn tại */
    public function test_get_non_existent_payment_returns_error()
    {
        $this->actingAs($this->customer, 'sanctum');

        $response = $this->getJson('/api/v1/payments/99999');

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Không tìm thấy thanh toán'
            ]);
    }

    /** @test - Không thể xem thanh toán của user khác */
    public function test_cannot_view_other_user_payment()
    {
        $otherUser = User::factory()->create(['VaiTro' => 'KhachHang']);
        
        $this->actingAs($otherUser, 'sanctum');

        $response = $this->getJson('/api/v1/payments/' . $this->thanhToan->MaThanhToan);

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Không tìm thấy thanh toán'
            ]);
    }

    /** @test - Tạo thanh toán mới thành công */
    public function test_create_payment_success()
    {
        $this->actingAs($this->customer, 'sanctum');

        // Tạo đơn hàng mới chưa có thanh toán
        $newOrder = DonHang::create([
            'MaNguoiDung' => $this->customer->MaNguoiDung,
            'TongTien' => 200000,
            'PhuongThucThanhToan' => 'ZaloPay',
            'TrangThai' => 'Chờ xử lý',
            'TenKhachHang' => 'New Customer',
            'SoDienThoai' => '0987654321',
            'DiaChiGiaoHang' => 'New Address'
        ]);

        $response = $this->postJson('/api/v1/payments', [
            'order_id' => $newOrder->MaDonHang,
            'payment_method' => 'ZaloPay',
            'success' => true
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Thanh toán thành công'
            ]);

        $this->assertDatabaseHas('thanh_toan', [
            'MaDonHang' => $newOrder->MaDonHang,
            'PhuongThuc' => 'ZaloPay',
            'TrangThai' => 'Đã thanh toán'
        ]);
    }

    /** @test - Không thể thanh toán đơn của user khác */
    public function test_cannot_pay_other_user_order()
    {
        $otherUser = User::factory()->create(['VaiTro' => 'KhachHang']);
        
        $this->actingAs($otherUser, 'sanctum');

        $response = $this->postJson('/api/v1/payments', [
            'order_id' => $this->donHang->MaDonHang,
            'payment_method' => 'MoMo'
        ]);

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Không tìm thấy đơn hàng'
            ]);
    }

    /** @test - Không thể thanh toán đơn đã thanh toán */
    public function test_cannot_pay_already_paid_order()
    {
        $this->actingAs($this->customer, 'sanctum');

        // Đánh dấu đã thanh toán
        $this->thanhToan->update(['TrangThai' => 'Đã thanh toán']);

        $response = $this->postJson('/api/v1/payments', [
            'order_id' => $this->donHang->MaDonHang,
            'payment_method' => 'MoMo'
        ]);

        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
                'message' => 'Đơn hàng đã được thanh toán'
            ]);
    }

    /** @test - Thanh toán thất bại (mô phỏng) */
    public function test_payment_failure_simulation()
    {
        $this->actingAs($this->customer, 'sanctum');

        // Tạo đơn mới
        $newOrder = DonHang::create([
            'MaNguoiDung' => $this->customer->MaNguoiDung,
            'TongTien' => 100000,
            'PhuongThucThanhToan' => 'VNPay',
            'TrangThai' => 'Chờ xử lý',
            'TenKhachHang' => 'Test',
            'SoDienThoai' => '0123456789',
            'DiaChiGiaoHang' => 'Test'
        ]);

        $response = $this->postJson('/api/v1/payments', [
            'order_id' => $newOrder->MaDonHang,
            'payment_method' => 'VNPay',
            'success' => false // Mô phỏng thất bại
        ]);

        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
                'message' => 'Thanh toán thất bại'
            ]);

        $this->assertDatabaseHas('thanh_toan', [
            'MaDonHang' => $newOrder->MaDonHang,
            'TrangThai' => 'Thất bại'
        ]);
    }

    /** @test - Thiếu trường bắt buộc khi tạo thanh toán */
    public function test_create_payment_with_missing_fields_returns_error()
    {
        $this->actingAs($this->customer, 'sanctum');

        $response = $this->postJson('/api/v1/payments', [
            'order_id' => $this->donHang->MaDonHang
            // Thiếu payment_method
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['payment_method']);
    }

    /** @test - Đơn hàng không tồn tại */
    public function test_create_payment_with_invalid_order_returns_error()
    {
        $this->actingAs($this->customer, 'sanctum');

        $response = $this->postJson('/api/v1/payments', [
            'order_id' => 99999,
            'payment_method' => 'MoMo'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['order_id']);
    }

    /** @test - Phương thức thanh toán không hợp lệ */
    public function test_create_payment_with_invalid_method_returns_error()
    {
        $this->actingAs($this->customer, 'sanctum');

        $response = $this->postJson('/api/v1/payments', [
            'order_id' => $this->donHang->MaDonHang,
            'payment_method' => 'INVALID_METHOD'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['payment_method']);
    }

    /** @test - Thanh toán COD */
    public function test_payment_with_cod_method()
    {
        $this->actingAs($this->customer, 'sanctum');

        $codOrder = DonHang::create([
            'MaNguoiDung' => $this->customer->MaNguoiDung,
            'TongTien' => 80000,
            'PhuongThucThanhToan' => 'COD',
            'TrangThai' => 'Chờ xử lý',
            'TenKhachHang' => 'COD Customer',
            'SoDienThoai' => '0123456789',
            'DiaChiGiaoHang' => 'COD Address'
        ]);

        $response = $this->postJson('/api/v1/payments', [
            'order_id' => $codOrder->MaDonHang,
            'payment_method' => 'COD'
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true
            ]);

        $this->assertDatabaseHas('thanh_toan', [
            'MaDonHang' => $codOrder->MaDonHang,
            'PhuongThuc' => 'COD',
            'TrangThai' => 'Đã thanh toán'
        ]);
    }
}
