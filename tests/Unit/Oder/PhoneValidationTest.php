<?php

namespace Tests\Unit\Order;

use Tests\TestCase;
use App\Services\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PhoneValidationTest extends TestCase
{
    use RefreshDatabase;
    protected $orderService;
    protected function setUp(): void
    {
        parent::setUp();
        $this->orderService = new OrderService();
    }
    public function test_7e8_phone_empty_returns_error()
    {
        $emptyPhones = ['', '   ', null];

        foreach ($emptyPhones as $phone) {
            $result = $this->orderService->validatePhone($phone ?? '');

            $this->assertFalse($result['success']);
            $this->assertEquals('Vui lòng điền số điện thoại người nhận hàng (7E.8)', $result['message']);
        }
    }
    public function test_7e9_phone_must_be_positive_integer_returns_error()
    {
        $invalidPhones = [
            'abc1234567',
            '091-234-5678',
            '091.234.5678',
            '091 234 5678',
            '+84912345678',
            '091abc45678',
            '091@234567'
        ];

        foreach ($invalidPhones as $phone) {
            $result = $this->orderService->validatePhone($phone);

            $this->assertFalse($result['success'], "Phone '{$phone}' should be invalid");
            $this->assertEquals('Vui lòng điền số điện thoại là số nguyên dương (7E.9)', $result['message']);
        }
    }

    // 7E.10 - Vui lòng điền số điện thoại có 10-11 chữ số
    public function test_7e10_phone_invalid_length_returns_error()
    {
        $invalidLengthPhones = [
            '091234567',      // 9 chữ số
            '09123456789012', // 14 chữ số
            '0123',           // 4 chữ số
            '123456789012345' // 15 chữ số
        ];

        foreach ($invalidLengthPhones as $phone) {
            $result = $this->orderService->validatePhone($phone);

            $this->assertFalse($result['success'], "Phone '{$phone}' should have invalid length");
            $this->assertEquals('Vui lòng điền số điện thoại có 10-11 chữ số (7E.10)', $result['message']);
        }
    }

    // Số điện thoại hợp lệ 10 chữ số
    public function test_valid_10_digit_phones_return_success()
    {
        $validPhones = [
            '0912345678',
            '0987654321',
            '0123456789',
            '0909123456',
            '0868123456'
        ];
        foreach ($validPhones as $phone) {
            $phoneResult = $this->orderService->validatePhone($phone);
            $this->assertTrue($phoneResult['success'], "Phone '{$phone}' should be valid");
        }
    }
    // Số điện thoại hợp lệ 11 chữ số
    public function test_valid_11_digit_phones_return_success()
    {
        $validPhones = [
            '84912345678',
            '84876543210',
            '84123456789'
        ];
        foreach ($validPhones as $phone) {
            $phoneResult = $this->orderService->validatePhone($phone);
            $this->assertTrue($phoneResult['success'], "Phone '{$phone}' should be valid");
        }
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


// // Các đầu số di động Việt Nam hợp lệ
//     public function test_vietnamese_mobile_prefixes_return_success()
//     {
//         $validPrefixes = [
//             '090',
//             '093',
//             '070',
//             '079',
//             '077',
//             '076',
//             '078',
//             '091',
//             '094',
//             '088',
//             '083',
//             '084',
//             '085',
//             '081',
//             '082'
//         ];

//         foreach ($validPrefixes as $prefix) {
//             $phone = $prefix . '1234567';
//             $phoneResult = $this->orderService->validatePhone($phone);
//             $this->assertTrue($phoneResult['success'], "Phone with prefix '{$prefix}' should be valid");
//         }
//     }

//     // Số điện thoại bàn hợp lệ
//     public function test_valid_landline_phones_return_success()
//     {
//         $validLandlines = [
//             '02812345678', // TP.HCM
//             '02436789012', // Hà Nội
//             '02363456789'  // Đà Nẵng
//         ];

//         foreach ($validLandlines as $phone) {
//             $phoneResult = $this->orderService->validatePhone($phone);
//             $this->assertTrue($phoneResult['success'], "Landline '{$phone}' should be valid");
//         }
//     }