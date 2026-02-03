<?php

namespace Tests\Unit\Voucher;

use Tests\TestCase;
use App\Services\VoucherService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PercentValueTest extends TestCase
{
    use RefreshDatabase;
    protected $voucherService;
    protected function setUp(): void
    {
        parent::setUp();
        $this->voucherService = new VoucherService();
    }
    public function test_8e5_percent_value_empty_returns_error()
    {
        $voucherData = $this->getValidVoucherData(['PhanTram' => '']);
        $result = $this->voucherService->validateVoucherData($voucherData);
        $this->assertFalse($result['success']);
        $this->assertEquals('Phần trăm giảm không được bỏ trống (8E.5)', $result['message']);
    }

    /** 8E.6 - Phần trăm giảm phải lớn hơn 0 */
    public function test_8e6_percent_value_must_be_greater_than_zero()
    {
        $invalidValues = [0, -5, -10.50];
        foreach ($invalidValues as $value) {
            $voucherData = $this->getValidVoucherData(['PhanTram' => $value]);
            $result = $this->voucherService->validateVoucherData($voucherData);
            $this->assertFalse($result['success'], "Value '{$value}' should be invalid");
            $this->assertEquals('Phần trăm giảm phải lớn hơn 0 (8E.6)', $result['message']);
        }
    }

    /** 8E.7 - Phần trăm giảm tối đa là 100 */
    public function test_8e7_percent_value_max_100_returns_error()
    {
        $invalidValues = [100.01, 150.00, 999.99];

        foreach ($invalidValues as $value) {
            $voucherData = $this->getValidVoucherData(['PhanTram' => $value]);
            $result = $this->voucherService->validateVoucherData($voucherData);

            $this->assertFalse($result['success'], "Value '{$value}' should be invalid");
            $this->assertEquals('Phần trăm giảm tối đa là 100 (8E.7)', $result['message']);
        }
    }
    public function test_8e8_percent_value_invalid_decimal_format_returns_error()
    {
        $invalidValues = [
            'abc',           // Không phải số
            '10.123',        // Quá 2 chữ số thập phân
            '1000.00',       // Quá 5 chữ số tổng
            '10.1234'        // Quá 2 chữ số thập phân
        ];
        foreach ($invalidValues as $value) {
            $voucherData = $this->getValidVoucherData(['PhanTram' => $value]);
            $result = $this->voucherService->validateVoucherData($voucherData);

            $this->assertFalse($result['success'], "Value '{$value}' should be invalid");
            $this->assertEquals('Phần trăm giảm không đúng định dạng (8E.8)', $result['message']);
        }
    }

    /** - Phần trăm hợp lệ với các giá trị khác nhau */
    public function test_valid_percent_values_return_success()
    {
        $validValues = [0.01, 5.00, 10.50, 25.75, 50.00, 99.99, 100.00];

        foreach ($validValues as $value) {
            $voucherData = $this->getValidVoucherData(['PhanTram' => $value]);
            $result = $this->voucherService->validateVoucherData($voucherData);

            $this->assertTrue($result['success'], "Value '{$value}' should be valid");
        }
    }
    private function getValidVoucherData($override = [])
    {
        return array_merge([
            'MaCode' => 'SALE2024',
            'PhanTram' => 15.50,
            'NgayBatDau' => now()->format('Y-m-d'),
            'NgayKetThuc' => now()->addDays(7)->format('Y-m-d')
        ], $override);
    }
}


    // /** - Phần trăm với 1 chữ số thập phân */
    // public function test_percent_value_one_decimal_place_returns_success()
    // {
    //     $voucherData = $this->getValidVoucherData(['PhanTram' => 15.5]);

    //     $result = $this->voucherService->validateVoucherData($voucherData);

    //     $this->assertTrue($result['success']);
    // }

    // /** - Phần trăm với 2 chữ số thập phân */
    // public function test_percent_value_two_decimal_places_returns_success()
    // {
    //     $voucherData = $this->getValidVoucherData(['PhanTram' => 25.75]);

    //     $result = $this->voucherService->validateVoucherData($voucherData);

    //     $this->assertTrue($result['success']);
    // }

    // /** - Phần trăm số nguyên */
    // public function test_percent_value_integer_returns_success()
    // {
    //     $voucherData = $this->getValidVoucherData(['PhanTram' => 20]);

    //     $result = $this->voucherService->validateVoucherData($voucherData);

    //     $this->assertTrue($result['success']);
    // }

    // /** - Phần trăm = 100.00 (giá trị biên) */
    // public function test_percent_value_exactly_100_returns_success()
    // {
    //     $voucherData = $this->getValidVoucherData(['PhanTram' => 100.00]);

    //     $result = $this->voucherService->validateVoucherData($voucherData);

    //     $this->assertTrue($result['success']);
    // }

    // /** - Phần trăm = 0.01 (giá trị tối thiểu) */
    // public function test_percent_value_minimum_valid_returns_success()
    // {
    //     $voucherData = $this->getValidVoucherData(['PhanTram' => 0.01]);

    //     $result = $this->voucherService->validateVoucherData($voucherData);

    //     $this->assertTrue($result['success']);
    // }
    
    // Test thành công với các giá trị phần trăm hợp lệ
    // public function test_comprehensive_valid_percent_values_returns_success()
    // {
    //     $validValues = [
    //         0.01,    // Giá trị tối thiểu
    //         1.00,    // Số nguyên nhỏ
    //         5.50,    // Decimal 1 chữ số
    //         10.25,   // Decimal 2 chữ số
    //         25.00,   // 25%
    //         50.75,   // 50%+
    //         99.99,   // Gần 100%
    //         100.00   // Giá trị tối đa
    //     ];

    //     foreach ($validValues as $value) {
    //         $voucherData = $this->getValidVoucherData(['PhanTram' => $value]);
    //         $result = $this->voucherService->validateVoucherData($voucherData);

    //         $this->assertTrue($result['success'], "Percent value '{$value}' should be valid");
    //     }
    // }