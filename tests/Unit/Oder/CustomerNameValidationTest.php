<?php

namespace Tests\Unit\Order;

use Tests\TestCase;
use App\Services\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CustomerNameValidationTest extends TestCase
{
    use RefreshDatabase;
    protected $orderService;
    protected function setUp(): void
    {
        parent::setUp();
        $this->orderService = new OrderService();
    }
    public function test_7e4_customer_name_empty_returns_error()
    {
        $emptyNames = ['', '   ', null];
        foreach ($emptyNames as $name) {
            $result = $this->orderService->validateCustomerName($name);

            $this->assertFalse($result['success']);
            $this->assertEquals('Họ tên người nhận hàng không được để trống (7E.4)', $result['message']);
        }
    }

    // 7E.5 - Vui lòng điền họ tên lớn hơn 10 ký tự
    public function test_7e5_customer_name_too_short_returns_error()
    {
        $shortNames = ['A', 'Abc', 'Nguyễn A', 'Test Name'];
        foreach ($shortNames as $name) {
            if (strlen($name) <= 10) {
                $result = $this->orderService->validateCustomerName($name);

                $this->assertFalse($result['success'], "Name '{$name}' should be too short");
                $this->assertEquals('Vui lòng điền họ tên lớn hơn 10 ký tự (7E.5)', $result['message']);
            }
        }
    }
    public function test_7e6_customer_name_too_long_returns_error()
    {
        $longName = str_repeat('A', 101);
        $result = $this->orderService->validateCustomerName($longName);

        $this->assertFalse($result['success']);
        $this->assertEquals('Vui lòng điền họ tên nhỏ hơn 100 ký tự (7E.6)', $result['message']);
    }

    // 7E.7 - Họ tên chỉ có chữ cái
    public function test_7e7_customer_name_only_letters_returns_error()
    {
        $invalidNames = [
            'Nguyễn Văn An123456',
            'John Smith Test 2024',
            'Trần Thị Bảo An@2024',
            'Test Name Number #12345',
            'User Test Name!@#$%'
        ];

        foreach ($invalidNames as $name) {
            $result = $this->orderService->validateCustomerName($name);

            $this->assertFalse($result['success'], "Name '{$name}' should contain invalid characters");
            $this->assertEquals('Họ tên chỉ có chữ cái (7E.7)', $result['message']);
        }
    }
    // Họ tên hợp lệ
    public function test_valid_customer_names_return_success()
    {
        $validNames = [
            'Nguyễn Văn An',
            'Trần Thị Bình An',
            'Lê Hoàng Nam Khánh',
            'Phạm Thị Thu Hương',
            'Đỗ Minh Tuấn Anh'
        ];

        foreach ($validNames as $name) {
            if (strlen($name) > 10 && strlen($name) < 100) {
                $orderData = $this->getValidOrderData(['HoTen' => $name]);

                $nameResult = $this->orderService->validateCustomerName($name);
                $this->assertTrue($nameResult['success'], "Name '{$name}' should be valid");
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

// // Họ tên đúng 11 ký tự (biên tối thiểu)
//     public function test_customer_name_exactly_11_characters_returns_success()
//     {
//         $name = 'Nguyễn Nam'; // Đúng 11 ký tự
//         $nameResult = $this->orderService->validateCustomerName($name);
//         $this->assertTrue($nameResult['success']);
//     }

//     // Họ tên đúng 99 ký tự (biên tối đa)
//     public function test_customer_name_exactly_99_characters_returns_success()
//     {
//         $name = str_repeat('A', 99);
//         $nameResult = $this->orderService->validateCustomerName($name);
//         // Sẽ fail ở validation chỉ có chữ cái, nhưng về length thì OK
//         $lengthOk = strlen($name) < 100;
//         $this->assertTrue($lengthOk);
//     }