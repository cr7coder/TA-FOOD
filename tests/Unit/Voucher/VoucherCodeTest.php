<?php

namespace Tests\Unit\Voucher;

use Tests\TestCase;
use App\Services\VoucherService;
use App\Models\GiamGia;
use Illuminate\Foundation\Testing\RefreshDatabase;

class VoucherCodeTest extends TestCase
{
    use RefreshDatabase;
    protected $voucherService;
    protected function setUp(): void
    {
        parent::setUp();
        $this->voucherService = new VoucherService();
    }
    public function test_8e1_voucher_code_empty_returns_error()
    {
        $voucherData = $this->getValidVoucherData(['MaCode' => '']);

        $result = $this->voucherService->validateVoucherData($voucherData);

        $this->assertFalse($result['success']);
        $this->assertEquals('Mã giảm giá không được bỏ trống (8E.1)', $result['message']);
    }
    public function test_8e2_voucher_code_exceeds_12_characters_returns_error()
    {
        $longCode = 'ABCDEFGHIJKLM'; // 13 ký tự
        $voucherData = $this->getValidVoucherData(['MaCode' => $longCode]);
        $result = $this->voucherService->validateVoucherData($voucherData);
        $this->assertFalse($result['success']);
        $this->assertEquals('Mã giảm giá không quá 12 ký tự (8E.2)', $result['message']);
    }
    public function test_8e3_voucher_code_contains_special_characters_returns_error()
    {
        $invalidCodes = ['SALE@123', 'CODE#456', 'TEST!789', 'ABC%DEF', 'SALE-50'];
        foreach ($invalidCodes as $code) {
            $voucherData = $this->getValidVoucherData(['MaCode' => $code]);
            $result = $this->voucherService->validateVoucherData($voucherData);
            $this->assertFalse($result['success']);
            $this->assertEquals('Mã giảm giá không chứa ký tự đặc biệt (8E.3)', $result['message']);
        }
    }
    public function test_8e4_voucher_code_already_exists_returns_error()
    {
        GiamGia::factory()->create(['MaCode' => 'EXISTING123']);
        $voucherData = $this->getValidVoucherData(['MaCode' => 'EXISTING123']);
        $result = $this->voucherService->validateVoucherData($voucherData);
        $this->assertFalse($result['success']);
        $this->assertEquals('Mã giảm giá đã tồn tại (8E.4)', $result['message']);
    }

    // Test thành công với các mã giảm giá hợp lệ
    public function test_valid_voucher_code_returns_success()
    {
        $validCodes = [
            'SALE2024',      // Chữ và số
            'ABCDEFGHIJKL',  // 12 ký tự chữ
            '123456789012',  // 12 ký tự số
            'ABC123',        // Kết hợp chữ số
            'NEWYEAR',       // Chữ thường
            'SUMMER25'       // Kết hợp chữ số
        ];

        foreach ($validCodes as $code) {
            $voucherData = $this->getValidVoucherData(['MaCode' => $code]);
            $result = $this->voucherService->validateVoucherData($voucherData);

            $this->assertTrue($result['success'], "Code '{$code}' should be valid");
        }
    }

    private function getValidVoucherData($override = [])
    {
        return array_merge([
            'MaCode' => 'SALE2024',
            'PhanTram' => 10.00,
            'NgayBatDau' => now()->addDay()->format('Y-m-d'),
            'NgayKetThuc' => now()->addDays(7)->format('Y-m-d')
        ], $override);
    }
}
