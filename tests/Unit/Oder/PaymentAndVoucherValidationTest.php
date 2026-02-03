<?php

namespace Tests\Unit\Order;

use Tests\TestCase;
use App\Services\OrderService;
use App\Models\GiamGia;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PaymentAndVoucherValidationTest extends TestCase
{
    use RefreshDatabase;

    protected $orderService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->orderService = new OrderService();
    }

    // 7E.15 - Vui lòng chọn phương thức thanh toán
    public function test_7e15_payment_method_empty_returns_error()
    {
        $emptyPaymentMethods = ['', '   ', null];

        foreach ($emptyPaymentMethods as $method) {
            $result = $this->orderService->validatePaymentMethod($method ?? '');

            $this->assertFalse($result['success']);
            $this->assertEquals('Vui lòng chọn phương thức thanh toán (7E.15)', $result['message']);
        }
    }

    // 7E.16 - Thanh toán không thành công, vui lòng thử lại
    public function test_7e16_payment_failed_returns_error()
    {
        // Mock payment gateway failure
        $orderData = $this->getValidOrderData([
            'PhuongThucThanhToan' => 'VNPay',
            'simulate_payment_failure' => true
        ]);

        $result = $this->orderService->processPayment($orderData);

        $this->assertFalse($result['success']);
        $this->assertEquals('Thanh toán không thành công, vui lòng thử lại (7E.16)', $result['message']);
    }

    // 7E.17 - Mã voucher không tồn tại
    public function test_7e17_voucher_not_exists_returns_error()
    {
        $result = $this->orderService->validateVoucher('NONEXIST12'); // 12 ký tự để pass format check

        $this->assertFalse($result['success']);
        $this->assertEquals('Mã voucher không tồn tại (7E.17)', $result['message']);
    }

    // 7E.18 - Mã voucher chỉ được chứa chữ cái và số
    public function test_7e18_voucher_invalid_characters_returns_error()
    {
        $invalidVouchers = [
            'SALE@2024',
            'VOUCHER#50',
            'DISCOUNT!',
            'CODE%10',
            'SALE-OFF',
            'VOUCHER 50',
            'CODE.123'
        ];

        foreach ($invalidVouchers as $voucher) {
            $result = $this->orderService->validateVoucherFormat($voucher);

            $this->assertFalse($result['success'], "Voucher '{$voucher}' should be invalid");
            $this->assertEquals('Mã voucher chỉ được chứa chữ cái và số (7E.18)', $result['message']);
        }
    }

    // 7E.19 - Mã voucher không được vượt quá 12 ký tự
    public function test_7e19_voucher_exceeds_12_characters_returns_error()
    {
        $longVoucher = 'ABCDEFGHIJKLM'; // 13 ký tự
        $result = $this->orderService->validateVoucherFormat($longVoucher);

        $this->assertFalse($result['success']);
        $this->assertEquals('Mã voucher không được vượt quá 12 ký tự (7E.19)', $result['message']);
    }

    // Phương thức thanh toán hợp lệ
    public function test_valid_payment_methods_return_success()
    {
        $validMethods = ['Tiền mặt', 'Chuyển khoản', 'VNPay', 'MoMo', 'ZaloPay'];

        foreach ($validMethods as $method) {
            $orderData = $this->getValidOrderData(['PhuongThucThanhToan' => $method]);

            $paymentResult = $this->orderService->validatePaymentMethod($method);
            $this->assertTrue($paymentResult['success'], "Payment method '{$method}' should be valid");
        }
    }

    // Mã voucher hợp lệ
    public function test_valid_vouchers_return_success()
    {
        // Tạo voucher trong database với khoảng ngày rộng hơn
        $validVoucher = GiamGia::create([
            'MaCode' => 'SALE2024',
            'PhanTram' => 15.00,
            'NgayBatDau' => now()->subDay()->format('Y-m-d'), // Bắt đầu từ hôm qua
            'NgayKetThuc' => now()->addDays(7)->format('Y-m-d') // Kết thúc 7 ngày sau
        ]);

        $voucherResult = $this->orderService->validateVoucher('SALE2024');
        $this->assertTrue($voucherResult['success']);
    }

    // Mã voucher với format hợp lệ
    public function test_valid_voucher_formats_return_success()
    {
        $validVoucherFormats = [
            'SALE2024',
            'VOUCHER123',
            'DISCOUNT50',
            'NEW2024',
            'FLASH10',
            'SUMMER24'
        ];

        foreach ($validVoucherFormats as $voucher) {
            if (strlen($voucher) <= 12) {
                $formatResult = $this->orderService->validateVoucherFormat($voucher);
                $this->assertTrue($formatResult['success'], "Voucher format '{$voucher}' should be valid");
            }
        }
    }

    // Mã voucher đúng 12 ký tự
    public function test_voucher_exactly_12_characters_returns_success()
    {
        $voucher = 'ABCDEFGHIJKL'; // Đúng 12 ký tự
        $formatResult = $this->orderService->validateVoucherFormat($voucher);
        $this->assertTrue($formatResult['success']);
    }

    // Thanh toán thành công
    public function test_payment_success_returns_success()
    {
        $orderData = $this->getValidOrderData([
            'PhuongThucThanhToan' => 'Tiền mặt'
        ]);

        $paymentResult = $this->orderService->processPayment($orderData);
        $this->assertTrue($paymentResult['success']);
    }

    private function getValidOrderData($override = [])
    {
        return array_merge([
            'HoTen' => 'Nguyễn Văn An',
            'SoDienThoai' => '0912345678',
            'DiaChi' => '123 Đường ABC, Quận 1, TP.HCM',
            'PhuongThucThanhToan' => 'Tiền mặt',
            'foods' => [
                ['MaMonAn' => 1, 'SoLuong' => 2]
            ]
        ], $override);
    }
}
