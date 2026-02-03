<?php

namespace Tests\Unit\Order;

use Tests\TestCase;
use App\Services\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AddressValidationTest extends TestCase
{
    use RefreshDatabase;
    protected $orderService;
    protected function setUp(): void
    {
        parent::setUp();
        $this->orderService = new OrderService();
    }
    public function test_7e11_address_empty_returns_error()
    {
        $emptyAddresses = ['', '   ', null];
        foreach ($emptyAddresses as $address) {
            $result = $this->orderService->validateAddress($address ?? '');

            $this->assertFalse($result['success']);
            $this->assertEquals('Vui lòng điền địa chỉ giao hàng (7E.11)', $result['message']);
        }
    }
    public function test_7e12_address_too_short_returns_error()
    {
        $shortAddresses = [
            'Hà Nội',      // 7 ký tự
            '123 ABC',     // 7 ký tự
            'TPHCM',       // 5 ký tự
            'Quận 1'       // 7 ký tự
        ];

        foreach ($shortAddresses as $address) {
            if (strlen($address) <= 10) {
                $result = $this->orderService->validateAddress($address);

                $this->assertFalse($result['success'], "Address '{$address}' should be too short");
                $this->assertEquals('Vui lòng điền địa chỉ giao hàng nhiều hơn 10 ký tự (7E.12)', $result['message']);
            }
        }
    }

    // 7E.13 - Vui lòng điền địa chỉ giao hàng ít hơn 200 ký tự
    public function test_7e13_address_too_long_returns_error()
    {
        $longAddress = str_repeat('A', 201);
        $result = $this->orderService->validateAddress($longAddress);

        $this->assertFalse($result['success']);
        $this->assertEquals('Vui lòng điền địa chỉ giao hàng ít hơn 200 ký tự (7E.13)', $result['message']);
    }

    // Địa chỉ hợp lệ với độ dài khác nhau
    public function test_valid_addresses_return_success()
    {
        $validAddresses = [
            '123 Lê Lợi, Q1', // 15 ký tự
            '456 Nguyễn Huệ, Phường 1, Quận 1, TP.HCM', // 44 ký tự
            'Số 10 Đường ABC, Phường XYZ, Quận 123, Thành phố DEF', // 57 ký tự
            'Tầng 5, Tòa nhà ABC, 123 Đường XYZ, Phường 1, Quận 1, TP.HCM' // 65 ký tự
        ];

        foreach ($validAddresses as $address) {
            if (strlen($address) > 10 && strlen($address) < 200) {
                $addressResult = $this->orderService->validateAddress($address);
                $this->assertTrue($addressResult['success'], "Address '{$address}' should be valid");
            }
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

// // Địa chỉ có ký tự đặc biệt hợp lệ
//     public function test_address_with_valid_special_characters_returns_success()
//     {
//         $validAddresses = [
//             '123/45 Lê Lợi, Q.1',
//             'Số 10, Đường ABC-XYZ',
//             'Tầng 3, 456 Nguyễn Huệ (gần chợ)',
//             '123 Nguyễn Thái Học, Hà Nội'
//         ];

//         foreach ($validAddresses as $address) {
//             if (strlen($address) > 10 && strlen($address) < 200) {
//                 $addressResult = $this->orderService->validateAddress($address);
//                 $this->assertTrue($addressResult['success'], "Address '{$address}' should be valid");
//             }
//         }
//     }
    // // Địa chỉ đúng 11 ký tự (biên tối thiểu)
    // public function test_address_exactly_11_characters_returns_success()
    // {
    //     $address = '123 ABC def'; // Đúng 11 ký tự
    //     $addressResult = $this->orderService->validateAddress($address);
    //     $this->assertTrue($addressResult['success']);
    // }

    // // Địa chỉ đúng 199 ký tự (biên tối đa)
    // public function test_address_exactly_199_characters_returns_success()
    // {
    //     $address = str_repeat('A', 199);
    //     $addressResult = $this->orderService->validateAddress($address);
    //     $this->assertTrue($addressResult['success']);
    // }
    // // Địa chỉ tiếng Việt có dấu
    // public function test_vietnamese_address_with_accents_returns_success()
    // {
    //     $vietnameseAddresses = [
    //         '123 Nguyễn Thái Học, Hà Nội',
    //         '456 Trần Hưng Đạo, TP.HCM',
    //         'Số 10 Lý Thường Kiệt, Đà Nẵng'
    //     ];

    //     foreach ($vietnameseAddresses as $address) {
    //         $addressResult = $this->orderService->validateAddress($address);
    //         $this->assertTrue($addressResult['success'], "Vietnamese address '{$address}' should be valid");
    //     }
    // }