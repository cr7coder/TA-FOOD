<?php

namespace Tests\Unit\Order;

use Tests\TestCase;
use App\Services\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class QuantityValidationTest extends TestCase
{
    use RefreshDatabase;
    protected $orderService;
    protected function setUp(): void
    {
        parent::setUp();
        $this->orderService = new OrderService();
    }
    public function test_7e2_quantity_must_be_positive_integer_returns_error()
    {
        $invalidQuantities = ['abc', '2.5', '-5', 'không', '', -1, -10];

        foreach ($invalidQuantities as $quantity) {
            $result = $this->orderService->validateQuantity($quantity);

            $this->assertFalse($result['success'], "Quantity '{$quantity}' should be invalid");
            $this->assertEquals('Số lượng món phải là số nguyên dương (7E.2)', $result['message']);
        }
    }
    public function test_7e3_quantity_must_be_greater_than_zero_returns_error()
    {
        $invalidQuantities = [0];
        foreach ($invalidQuantities as $quantity) {
            $result = $this->orderService->validateQuantity($quantity);
            $this->assertFalse($result['success'], "Quantity '{$quantity}' should be invalid");
            $this->assertEquals('Số lượng món phải lớn hơn 0 (7E.3)', $result['message']);
        }
    }
    public function test_valid_quantities_return_success()
    {
        $validQuantities = [1, 2, 5, 10, 99];
        foreach ($validQuantities as $quantity) {
            $quantityResult = $this->orderService->validateQuantity($quantity);
            $this->assertTrue($quantityResult['success'], "Quantity '{$quantity}' should be valid");
        }
    }
    public function test_string_number_quantity_returns_success()
    {
        $validStringQuantities = ['1', '5', '10'];

        foreach ($validStringQuantities as $quantity) {
            $result = $this->orderService->validateQuantity($quantity);
            $this->assertTrue($result['success'], "String quantity '{$quantity}' should be valid");
        }
    }
    private function getValidOrderData($override = [])
    {
        return array_merge([
            'HoTen' => 'Nguyễn Văn An',
            'SoDienThoai' => '0912345678',
            'DiaChi' => '123 Đường ABC, Quận 1, TP.HCM',
            'PhuongThucThanhToan' => 'Tiền mặt',
            'foods' => []
        ], $override);
    }
}
