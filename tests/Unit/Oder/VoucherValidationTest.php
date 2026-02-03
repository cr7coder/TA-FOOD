<?php

namespace Tests\Unit\Order;

use Tests\TestCase;
use App\Services\OrderService;
use App\Models\GiamGia;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

class VoucherValidationTest extends TestCase
{
    use RefreshDatabase;
    protected $orderService;
    protected function setUp(): void
    {
        parent::setUp();
        $this->orderService = new OrderService();
    }
    public function test_7e17_voucher_not_exists_returns_error()
    {
        $nonExistentVouchers = ['NOTEXIST123', 'FAKE2024', 'INVALID999'];

        foreach ($nonExistentVouchers as $voucher) {
            // Sử dụng validateVoucher trực tiếp thay vì validateOrderData
            $result = $this->orderService->validateVoucher($voucher);

            $this->assertFalse($result['success'], "Voucher '{$voucher}' should not exist");
            $this->assertEquals('Mã voucher không tồn tại (7E.17)', $result['message']);
        }
    }
    public function test_7e18_voucher_invalid_characters_returns_error()
    {
        $invalidVouchers = [
            'SALE@2024',     // Có @
            'VOUCHER#50',    // Có #
            'DISCOUNT!',     // Có !
            'CODE%10',       // Có %
            'SALE-OFF',      // Có -
            'VOUCHER 50',    // Có khoảng trắng
            'CODE.123',      // Có .
            'SALE&SAVE',     // Có &
            'VOUCHER+10',    // Có +
            'CODE*2024'      // Có *
        ];

        foreach ($invalidVouchers as $voucher) {
            // Sử dụng validateVoucherFormat trực tiếp
            $result = $this->orderService->validateVoucherFormat($voucher);

            $this->assertFalse($result['success'], "Voucher '{$voucher}' should be invalid");
            $this->assertEquals('Mã voucher chỉ được chứa chữ cái và số (7E.18)', $result['message']);
        }
    }

    // 7E.19 - Mã voucher không được vượt quá 12 ký tự
    public function test_7e19_voucher_exceeds_12_characters_returns_error()
    {
        $longVouchers = [
            'ABCDEFGHIJKLM',        // 13 ký tự
            'VERYLONGVOUCHER123',   // 18 ký tự
            'SUPERSALEPROMO2024',   // 19 ký tự
            str_repeat('A', 15),    // 15 ký tự
            str_repeat('1', 20)     // 20 ký tự
        ];

        foreach ($longVouchers as $voucher) {
            // Sử dụng validateVoucherFormat trực tiếp
            $result = $this->orderService->validateVoucherFormat($voucher);

            $this->assertFalse($result['success'], "Voucher '{$voucher}' should exceed 12 characters");
            $this->assertEquals('Mã voucher không được vượt quá 12 ký tự (7E.19)', $result['message']);
        }
    }
    // Mã voucher hợp lệ về format
    public function test_valid_voucher_formats_return_success()
    {
        $validVoucherFormats = [
            'SALE2024',      // 8 ký tự
            'VOUCHER123',    // 10 ký tự
            'DISCOUNT50',    // 11 ký tự
            'ABCDEFGHIJKL',  // 12 ký tự (max)
            'NEW2024',       // 7 ký tự
            'FLASH10',       // 7 ký tự
            'SUMMER24',      // 8 ký tự
            'A',             // 1 ký tự
            '123',           // 3 ký tự số
            'ABC123DEF456'   // 12 ký tự mix
        ];

        foreach ($validVoucherFormats as $voucher) {
            if (strlen($voucher) <= 12) {
                $formatResult = $this->orderService->validateVoucherFormat($voucher);
                $this->assertTrue($formatResult['success'], "Voucher format '{$voucher}' should be valid");
            }
        }
    }

    // Áp dụng voucher giảm giá thành công
    public function test_apply_voucher_discount_success()
    {
        // Tạo voucher hợp lệ
        $voucher = GiamGia::create([
            'MaCode' => 'DISCOUNT20',
            'PhanTram' => 20.00,
            'NgayBatDau' => Carbon::today()->subDay()->format('Y-m-d'), // Hôm qua
            'NgayKetThuc' => Carbon::today()->addDays(30)->format('Y-m-d') // 30 ngày sau
        ]);

        $totalAmount = 100000; // 100k
        $result = $this->orderService->applyVoucherDiscount($totalAmount, 'DISCOUNT20');

        $this->assertTrue($result['success']);
        $this->assertEquals(100000, $result['original_total']);
        $this->assertEquals(20000, $result['discount_amount']); // 20% của 100k
        $this->assertEquals(80000, $result['final_total']); // 100k - 20k
        $this->assertEquals('DISCOUNT20', $result['voucher']->MaCode);
    }

    // Áp dụng voucher với các mức giảm khác nhau
    public function test_apply_different_discount_percentages()
    {
        $testCases = [
            ['code' => 'DISCOUNT5', 'percent' => 5.00, 'total' => 200000, 'expected_discount' => 10000],
            ['code' => 'DISCOUNT10', 'percent' => 10.50, 'total' => 150000, 'expected_discount' => 15750],
            ['code' => 'DISCOUNT50', 'percent' => 50.00, 'total' => 80000, 'expected_discount' => 40000],
            ['code' => 'DISCOUNT99', 'percent' => 99.99, 'total' => 100000, 'expected_discount' => 99990]
        ];

        foreach ($testCases as $case) {
            // Tạo voucher
            GiamGia::create([
                'MaCode' => $case['code'],
                'PhanTram' => $case['percent'],
                'NgayBatDau' => Carbon::today()->subDay()->format('Y-m-d'), // Hôm qua
                'NgayKetThuc' => Carbon::today()->addDays(7)->format('Y-m-d') // 7 ngày sau
            ]);

            $result = $this->orderService->applyVoucherDiscount($case['total'], $case['code']);

            $this->assertTrue($result['success'], "Voucher {$case['code']} should apply successfully");
            $this->assertEquals($case['expected_discount'], $result['discount_amount'], "Discount amount for {$case['code']} should be correct");
            $this->assertEquals($case['total'] - $case['expected_discount'], $result['final_total'], "Final total for {$case['code']} should be correct");
        }
    }

    private function getValidOrderData($override = [])
    {
        return array_merge([
            'HoTen' => 'Nguyễn Văn An Khang',
            'SoDienThoai' => '0912345678',
            'DiaChi' => '123 Đường ABC, Quận 1, TP.HCM',
            'PhuongThucThanhToan' => 'Tiền mặt',
            'foods' => [
                ['MaMonAn' => 1, 'SoLuong' => 2]
            ]
        ], $override);
    }
}

// // Mã voucher đúng 12 ký tự (biên tối đa)
//     public function test_voucher_exactly_12_characters_returns_success()
//     {
//         $voucher = 'ABCDEFGHIJKL'; // Đúng 12 ký tự
//         $formatResult = $this->orderService->validateVoucherFormat($voucher);
//         $this->assertTrue($formatResult['success']);
//     }

//     // Mã voucher chỉ có số
//     public function test_voucher_only_numbers_returns_success()
//     {
//         $numberVouchers = ['123456789', '2024', '999'];

//         foreach ($numberVouchers as $voucher) {
//             $formatResult = $this->orderService->validateVoucherFormat($voucher);
//             $this->assertTrue($formatResult['success'], "Number voucher '{$voucher}' should be valid");
//         }
//     }

//     // Mã voucher chỉ có chữ cái
//     public function test_voucher_only_letters_returns_success()
//     {
//         $letterVouchers = ['ABCDEF', 'SALE', 'VOUCHER', 'DISCOUNT'];

//         foreach ($letterVouchers as $voucher) {
//             $formatResult = $this->orderService->validateVoucherFormat($voucher);
//             $this->assertTrue($formatResult['success'], "Letter voucher '{$voucher}' should be valid");
//         }
//     }

//     // Mã voucher hỗn hợp chữ cái và số
//     public function test_voucher_alphanumeric_returns_success()
//     {
//         $alphanumericVouchers = ['SALE2024', 'ABC123', '123DEF', 'MIX2024ABC'];

//         foreach ($alphanumericVouchers as $voucher) {
//             if (strlen($voucher) <= 12) {
//                 $formatResult = $this->orderService->validateVoucherFormat($voucher);
//                 $this->assertTrue($formatResult['success'], "Alphanumeric voucher '{$voucher}' should be valid");
//             }
//         }
//     }

    // // Mã voucher tồn tại và còn hiệu lực
    // public function test_existing_valid_voucher_returns_success()
    // {
    //     // Tạo voucher trong database
    //     $validVoucher = GiamGia::create([
    //         'MaCode' => 'SALE2024',
    //         'PhanTram' => 15.00,
    //         'NgayBatDau' => Carbon::today()->subDay()->format('Y-m-d'), // Bắt đầu từ hôm qua
    //         'NgayKetThuc' => Carbon::today()->addDays(7)->format('Y-m-d') // Kết thúc 7 ngày sau
    //     ]);

    //     $voucherResult = $this->orderService->validateVoucher('SALE2024');
    //     $this->assertTrue($voucherResult['success']);
    //     $this->assertArrayHasKey('data', $voucherResult);
    //     $this->assertEquals('SALE2024', $voucherResult['data']->MaCode);
    // }

    // // Mã voucher hết hạn
    // public function test_expired_voucher_returns_error()
    // {
    //     // Tạo voucher đã hết hạn
    //     $expiredVoucher = GiamGia::create([
    //         'MaCode' => 'EXPIRED123',
    //         'PhanTram' => 20.00,
    //         'NgayBatDau' => Carbon::today()->subDays(10)->format('Y-m-d'),
    //         'NgayKetThuc' => Carbon::yesterday()->format('Y-m-d')
    //     ]);

    //     $result = $this->orderService->validateVoucher('EXPIRED123');

    //     $this->assertFalse($result['success']);
    //     $this->assertEquals('Mã voucher đã hết hạn', $result['message']);
    // }

    // // Mã voucher chưa đến ngày sử dụng
    // public function test_future_voucher_returns_error()
    // {
    //     // Tạo voucher chưa đến ngày sử dụng
    //     $futureVoucher = GiamGia::create([
    //         'MaCode' => 'FUTURE123',
    //         'PhanTram' => 25.00,
    //         'NgayBatDau' => Carbon::tomorrow()->format('Y-m-d'),
    //         'NgayKetThuc' => Carbon::today()->addDays(10)->format('Y-m-d')
    //     ]);

    //     $result = $this->orderService->validateVoucher('FUTURE123');

    //     $this->assertFalse($result['success']);
    //     $this->assertEquals('Mã voucher đã hết hạn', $result['message']); // Cùng message với expired
    // }
    // Voucher giảm 100%
    // public function test_voucher_100_percent_discount()
    // {
    //     $voucher = GiamGia::create([
    //         'MaCode' => 'FREE100',
    //         'PhanTram' => 100.00,
    //         'NgayBatDau' => Carbon::today()->subDay()->format('Y-m-d'), // Hôm qua
    //         'NgayKetThuc' => Carbon::today()->addDays(1)->format('Y-m-d') // Ngày mai
    //     ]);

    //     $totalAmount = 50000;
    //     $result = $this->orderService->applyVoucherDiscount($totalAmount, 'FREE100');

    //     $this->assertTrue($result['success']);
    //     $this->assertEquals(50000, $result['original_total']);
    //     $this->assertEquals(50000, $result['discount_amount']);
    //     $this->assertEquals(0, $result['final_total']); // Miễn phí hoàn toàn
    // }

    // // Áp dụng voucher cho đơn hàng nhỏ
    // public function test_apply_voucher_to_small_order()
    // {
    //     $voucher = GiamGia::create([
    //         'MaCode' => 'SMALL10',
    //         'PhanTram' => 10.00,
    //         'NgayBatDau' => Carbon::today()->subDay()->format('Y-m-d'), // Hôm qua
    //         'NgayKetThuc' => Carbon::today()->addDays(5)->format('Y-m-d') // 5 ngày sau
    //     ]);

    //     $smallAmount = 1000; // 1k
    //     $result = $this->orderService->applyVoucherDiscount($smallAmount, 'SMALL10');

    //     $this->assertTrue($result['success']);
    //     $this->assertEquals(1000, $result['original_total']);
    //     $this->assertEquals(100, $result['discount_amount']); // 10% của 1k
    //     $this->assertEquals(900, $result['final_total']);
    // }