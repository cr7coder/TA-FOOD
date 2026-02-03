<?php

namespace Tests\Unit\Order;

use Tests\TestCase;
use App\Services\OrderService;
use App\Models\MonAn;
use App\Models\NhaHang;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MenuAvailabilityTest extends TestCase
{
    use RefreshDatabase;

    protected $orderService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->orderService = new OrderService();
    }

    // 7E.1 - Thực đơn hiện không khả dụng
    public function test_7e1_menu_not_available_returns_error()
    {
        $restaurant = NhaHang::factory()->create();

        // Tạo món ăn không khả dụng
        $unavailableFood = MonAn::factory()->create([
            'MaNhaHang' => $restaurant->MaNhaHang,
            'TrangThai' => 'Ngừng bán'
        ]);

        $orderData = [
            'foods' => [
                ['MaMonAn' => $unavailableFood->MaMonAn, 'SoLuong' => 2]
            ]
        ];

        $result = $this->orderService->validateOrderData($orderData);

        $this->assertFalse($result['success']);
        $this->assertEquals('Thực đơn hiện không khả dụng (7E.1)', $result['message']);
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
// Món ăn khả dụng
    // public function test_available_menu_returns_success()
    // {
    //     $restaurant = NhaHang::factory()->create();

    //     $availableFood = MonAn::factory()->create([
    //         'MaNhaHang' => $restaurant->MaNhaHang,
    //         'TrangThai' => 'Còn bán'
    //     ]);

    //     $orderData = $this->getValidOrderData([
    //         'foods' => [
    //             ['MaMonAn' => $availableFood->MaMonAn, 'SoLuong' => 2]
    //         ]
    //     ]);

    //     $result = $this->orderService->validateOrderData($orderData);

    //     $this->assertTrue($result['success']);
    // }

    // // Nhà hàng đóng cửa
    // public function test_restaurant_closed_menu_not_available()
    // {
    //     $restaurant = NhaHang::factory()->create(['TrangThai' => 'Đóng cửa']);

    //     $food = MonAn::factory()->create([
    //         'MaNhaHang' => $restaurant->MaNhaHang,
    //         'TrangThai' => 'Còn bán'
    //     ]);

    //     $orderData = [
    //         'foods' => [
    //             ['MaMonAn' => $food->MaMonAn, 'SoLuong' => 1]
    //         ]
    //     ];

    //     $result = $this->orderService->validateOrderData($orderData);

    //     $this->assertFalse($result['success']);
    //     $this->assertEquals('Thực đơn hiện không khả dụng (7E.1)', $result['message']);
    // }