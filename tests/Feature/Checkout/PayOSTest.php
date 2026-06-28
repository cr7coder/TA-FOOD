<?php

namespace Tests\Feature\Checkout;

use Tests\TestCase;
use App\Models\User;
use App\Models\NhaHang;
use App\Models\MonAn;
use App\Models\DonHang;
use App\Models\DonHangChiTiet;
use App\Models\ThanhToan;
use App\Services\PayOSService;
use App\Services\NotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;

class PayOSTest extends TestCase
{
    use RefreshDatabase;

    protected $customer;
    protected $donHang;
    protected $thanhToan;
    protected $payosOrderCode;

    protected function setUp(): void
    {
        parent::setUp();

        // Cấu hình mock keys cho PayOS
        Config::set('services.payos.client_id', 'mock_client_id');
        Config::set('services.payos.api_key', 'mock_api_key');
        Config::set('services.payos.checksum_key', 'mock_checksum_key');

        // Tạo user với TrangThai là Hoạt động để không bị CheckUserStatus middleware chặn
        $this->customer = User::factory()->create([
            'VaiTro' => 'KhachHang',
            'TrangThai' => 'Hoạt động'
        ]);
        
        $restaurant = NhaHang::factory()->create();
        $food = MonAn::factory()->create([
            'MaNhaHang' => $restaurant->MaNhaHang,
            'Gia' => 50000
        ]);

        $this->payosOrderCode = 123456789;

        // Tạo đơn hàng mẫu
        $this->donHang = DonHang::create([
            'MaNguoiDung' => $this->customer->MaNguoiDung,
            'TongTien' => 150000,
            'PhuongThucThanhToan' => 'Online',
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

        // Tạo thanh toán mẫu với payos_order_code
        $this->thanhToan = ThanhToan::create([
            'MaDonHang' => $this->donHang->MaDonHang,
            'PhuongThuc' => 'VietQR',
            'SoTien' => 150000,
            'TrangThai' => 'Chờ thanh toán',
            'payos_order_code' => $this->payosOrderCode
        ]);
    }

    protected function tearDown(): void
    {
        // Xóa file thông báo của test user nếu có
        $path = storage_path("app/notifications_{$this->customer->MaNguoiDung}.json");
        if (File::exists($path)) {
            File::delete($path);
        }

        parent::tearDown();
    }

    /** @test - Kiểm tra signature generation của PayOSService */
    public function test_payos_service_signature_generation()
    {
        $service = new PayOSService();
        $data = [
            'amount' => 1000,
            'cancelUrl' => 'http://localhost/cancel',
            'description' => 'Thanh toan don hang',
            'orderCode' => 123456,
            'returnUrl' => 'http://localhost/return',
        ];

        $signature = $service->generateSignature($data);
        $this->assertNotEmpty($signature);

        // Chắc chắn cùng 1 data và key sẽ ra cùng 1 signature
        $this->assertEquals($signature, $service->generateSignature($data));
    }

    /** @test - Webhook trả về 400 nếu chữ ký không hợp lệ */
    public function test_webhook_returns_400_on_invalid_signature()
    {
        $payload = [
            'data' => [
                'orderCode' => $this->payosOrderCode,
                'amount' => 150000,
                'description' => 'Thanh toan TAFOOD',
                'code' => '00',
            ],
            'signature' => 'wrong_signature'
        ];

        $response = $this->postJson('/api/payos/webhook', $payload);

        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
                'message' => 'Chữ ký không hợp lệ!'
            ]);

        // Trạng thái DB không được thay đổi
        $this->thanhToan->refresh();
        $this->assertEquals('Chờ thanh toán', $this->thanhToan->TrangThai);
    }

    /** @test - Webhook cập nhật đơn hàng thành công khi chữ ký hợp lệ */
    public function test_webhook_confirms_payment_and_order_on_valid_signature()
    {
        $service = new PayOSService();
        
        $data = [
            'orderCode' => $this->payosOrderCode,
            'amount' => 150000,
            'description' => 'Thanh toan TAFOOD',
            'reference' => 'FT1234567890',
            'code' => '00',
        ];

        $payload = [
            'data' => $data,
            'signature' => $service->generateSignature($data)
        ];

        $response = $this->postJson('/api/payos/webhook', $payload);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Xử lý webhook thành công.'
            ]);

        // Kiểm tra db được cập nhật đúng
        $this->thanhToan->refresh();
        $this->donHang->refresh();

        $this->assertEquals('Đã thanh toán', $this->thanhToan->TrangThai);
        $this->assertEquals('FT1234567890', $this->thanhToan->MaGiaoDich);
        $this->assertEquals('Đã xác nhận', $this->donHang->TrangThai);

        // Kiểm tra thông báo được ghi nhận vào file JSON qua NotificationService
        $notifications = NotificationService::get($this->customer->MaNguoiDung);
        $this->assertNotEmpty($notifications);
        $this->assertEquals('✅ Thanh toán online thành công!', $notifications[0]['title']);
    }

    /** @test - Route Return chuyển hướng về trang success */
    public function test_checkout_payos_return_redirects_to_success()
    {
        $this->actingAs($this->customer);

        $response = $this->get('/checkout/payos-return?orderCode=' . $this->payosOrderCode);

        $response->assertRedirect(route('checkout.success', $this->donHang->MaDonHang));
    }

    /** @test - Route Cancel hủy thanh toán và đơn hàng */
    public function test_checkout_payos_cancel_updates_states_and_redirects()
    {
        $this->actingAs($this->customer);

        $response = $this->get('/checkout/payos-cancel?orderCode=' . $this->payosOrderCode);

        $response->assertRedirect(route('orders.history'));

        // DB check
        $this->thanhToan->refresh();
        $this->donHang->refresh();

        $this->assertEquals('Thất bại', $this->thanhToan->TrangThai);
        $this->assertEquals('Hủy', $this->donHang->TrangThai);
        $this->assertEquals('customer', $this->donHang->nguoi_huy);
    }
}
