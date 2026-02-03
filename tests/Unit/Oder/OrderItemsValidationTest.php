<?php

namespace Tests\Unit\Order;

use Tests\TestCase;
use App\Services\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderItemsValidationTest extends TestCase
{
    use RefreshDatabase;

    protected $orderService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->orderService = new OrderService();
    }

    // 7E.14 - Vui lòng chọn ít nhất 1 món ăn để đặt hàng
    public function test_7e14_no_food_items_returns_error()
    {
        $emptyFoodOrders = [
            [],
            null
        ];

        foreach ($emptyFoodOrders as $foodData) {
            $result = $this->orderService->validateOrderItems($foodData);

            $this->assertFalse($result['success']);
            $this->assertEquals('Vui lòng chọn ít nhất 1 món ăn để đặt hàng (7E.14)', $result['message']);
        }
    }

    // Có ít nhất 1 món ăn
    public function test_at_least_one_food_item_returns_success()
    {
        $orderData = $this->getValidOrderData([
            'foods' => [
                ['MaMonAn' => 1, 'SoLuong' => 2]
            ]
        ]);

        $itemsResult = $this->orderService->validateOrderItems($orderData['foods']);
        $this->assertTrue($itemsResult['success']);
    }

    // Nhiều món ăn
    public function test_multiple_food_items_return_success()
    {
        $orderData = $this->getValidOrderData([
            'foods' => [
                ['MaMonAn' => 1, 'SoLuong' => 2],
                ['MaMonAn' => 2, 'SoLuong' => 1],
                ['MaMonAn' => 3, 'SoLuong' => 3]
            ]
        ]);

        $itemsResult = $this->orderService->validateOrderItems($orderData['foods']);
        $this->assertTrue($itemsResult['success']);
    }

    // Món ăn với số lượng khác nhau
    public function test_food_items_with_different_quantities_return_success()
    {
        $orderData = $this->getValidOrderData([
            'foods' => [
                ['MaMonAn' => 1, 'SoLuong' => 1],
                ['MaMonAn' => 2, 'SoLuong' => 5],
                ['MaMonAn' => 3, 'SoLuong' => 10]
            ]
        ]);

        $itemsResult = $this->orderService->validateOrderItems($orderData['foods']);
        $this->assertTrue($itemsResult['success']);
    }

    // Món ăn trùng lặp (kiểm tra xử lý)
    public function test_duplicate_food_items_handling()
    {
        $orderData = $this->getValidOrderData([
            'foods' => [
                ['MaMonAn' => 1, 'SoLuong' => 2],
                ['MaMonAn' => 1, 'SoLuong' => 3] // Trùng MaMonAn
            ]
        ]);

        $itemsResult = $this->orderService->validateOrderItems($orderData['foods']);
        // Có thể success (gộp số lượng) hoặc error (không cho phép trùng)
        // Tùy theo business logic của bạn
        $this->assertIsArray($itemsResult);
        $this->assertArrayHasKey('success', $itemsResult);
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
