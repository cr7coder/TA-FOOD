<?php

namespace Tests\Unit\Voucher;

use Tests\TestCase;
use App\Services\VoucherService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EndDateTest extends TestCase
{
    use RefreshDatabase;
    protected $voucherService;
    protected function setUp(): void
    {
        parent::setUp();
        $this->voucherService = new VoucherService();
    }
    public function test_8e12_end_date_empty_returns_error()
    {
        $voucherData = $this->getValidVoucherData(['NgayKetThuc' => '']);

        $result = $this->voucherService->validateVoucherData($voucherData);

        $this->assertFalse($result['success']);
        $this->assertEquals('Ngày kết thúc không được bỏ trống (8E.12)', $result['message']);
    }
    public function test_8e13_end_date_invalid_format_returns_error()
    {
        $invalidDates = [
            '2024-13-01',      // Tháng không hợp lệ
            '2024-02-30',      // Ngày không tồn tại
            '2024/12/31',      // Sai format
            '31-12-2024',      // Sai format
            'invalid-date',    // Không phải ngày
            '2024-12-1',       // Thiếu số 0
            '24-12-31'         // Năm 2 chữ số
        ];

        foreach ($invalidDates as $date) {
            $voucherData = $this->getValidVoucherData(['NgayKetThuc' => $date]);
            $result = $this->voucherService->validateVoucherData($voucherData);

            $this->assertFalse($result['success'], "Date '{$date}' should be invalid");
            $this->assertEquals('Ngày kết thúc không hợp lệ (8E.13)', $result['message']);
        }
    }
    public function test_8e14_end_date_must_be_after_or_equal_start_date()
    {
        $startDate = Carbon::today()->addDays(5)->format('Y-m-d');
        $invalidEndDates = [
            Carbon::today()->format('Y-m-d'),                    // Trước start date
            Carbon::today()->addDays(1)->format('Y-m-d'),       // Trước start date
            Carbon::today()->addDays(4)->format('Y-m-d')        // Trước start date
        ];

        foreach ($invalidEndDates as $endDate) {
            $voucherData = $this->getValidVoucherData([
                'NgayBatDau' => $startDate,
                'NgayKetThuc' => $endDate
            ]);
            $result = $this->voucherService->validateVoucherData($voucherData);

            $this->assertFalse($result['success'], "End date '{$endDate}' should be invalid when start date is '{$startDate}'");
            $this->assertEquals('Ngày kết thúc phải sau hoặc bằng ngày bắt đầu (8E.14)', $result['message']);
        }
    }

    // Test thành công với các ngày kết thúc hợp lệ
    public function test_comprehensive_valid_end_dates_returns_success()
    {
        $baseStartDate = Carbon::today();
        $validEndDates = [
            $baseStartDate->format('Y-m-d'),                    // Cùng ngày với start date
            $baseStartDate->copy()->addDay()->format('Y-m-d'),  // 1 ngày sau
            $baseStartDate->copy()->addDays(3)->format('Y-m-d'), // 3 ngày sau
            $baseStartDate->copy()->addWeek()->format('Y-m-d'), // 1 tuần sau
            $baseStartDate->copy()->addMonth()->format('Y-m-d'), // 1 tháng sau
            $baseStartDate->copy()->addMonths(6)->format('Y-m-d'), // 6 tháng sau
            $baseStartDate->copy()->addYear()->format('Y-m-d'), // 1 năm sau
            '2027-12-31'                                        // Xa trong tương lai
        ];

        foreach ($validEndDates as $endDate) {
            $voucherData = $this->getValidVoucherData([
                'NgayBatDau' => $baseStartDate->format('Y-m-d'),
                'NgayKetThuc' => $endDate
            ]);
            $result = $this->voucherService->validateVoucherData($voucherData);

            $this->assertTrue($result['success'], "End date '{$endDate}' should be valid with start date '{$baseStartDate->format('Y-m-d')}'");
        }
    }

    private function getValidVoucherData($override = [])
    {
        return array_merge([
            'MaCode' => 'SALE2024',
            'PhanTram' => 15.50,
            'NgayBatDau' => Carbon::today()->format('Y-m-d'),
            'NgayKetThuc' => Carbon::today()->addDays(7)->format('Y-m-d')
        ], $override);
    }
}





    // /** - Ngày kết thúc hợp lệ với các khoảng cách khác nhau */
    // public function test_valid_end_dates_return_success()
    // {
    //     $startDate = Carbon::today()->format('Y-m-d');
    //     $validEndDates = [
    //         Carbon::today()->format('Y-m-d'),           // Cùng ngày
    //         Carbon::tomorrow()->format('Y-m-d'),        // 1 ngày sau
    //         Carbon::today()->addDays(7)->format('Y-m-d'), // 1 tuần sau
    //         Carbon::today()->addMonths(1)->format('Y-m-d'), // 1 tháng sau
    //         Carbon::today()->addYears(1)->format('Y-m-d')   // 1 năm sau
    //     ];

    //     foreach ($validEndDates as $endDate) {
    //         $voucherData = $this->getValidVoucherData([
    //             'NgayBatDau' => $startDate,
    //             'NgayKetThuc' => $endDate
    //         ]);
    //         $result = $this->voucherService->validateVoucherData($voucherData);

    //         $this->assertTrue($result['success'], "End date '{$endDate}' should be valid when start date is '{$startDate}'");
    //     }
    // }

    // /** - Ngày kết thúc = ngày bắt đầu */
    // public function test_end_date_equals_start_date_returns_success()
    // {
    //     $sameDate = Carbon::today()->format('Y-m-d');
    //     $voucherData = $this->getValidVoucherData([
    //         'NgayBatDau' => $sameDate,
    //         'NgayKetThuc' => $sameDate
    //     ]);

    //     $result = $this->voucherService->validateVoucherData($voucherData);

    //     $this->assertTrue($result['success']);
    // }

    // /** - Khoảng cách ngày hợp lệ 1 ngày */
    // public function test_one_day_duration_returns_success()
    // {
    //     $startDate = Carbon::today()->format('Y-m-d');
    //     $endDate = Carbon::tomorrow()->format('Y-m-d');

    //     $voucherData = $this->getValidVoucherData([
    //         'NgayBatDau' => $startDate,
    //         'NgayKetThuc' => $endDate
    //     ]);

    //     $result = $this->voucherService->validateVoucherData($voucherData);

    //     $this->assertTrue($result['success']);
    // }

    // /** - Khoảng cách ngày hợp lệ 1 tuần */
    // public function test_one_week_duration_returns_success()
    // {
    //     $startDate = Carbon::today()->format('Y-m-d');
    //     $endDate = Carbon::today()->addWeek()->format('Y-m-d');

    //     $voucherData = $this->getValidVoucherData([
    //         'NgayBatDau' => $startDate,
    //         'NgayKetThuc' => $endDate
    //     ]);

    //     $result = $this->voucherService->validateVoucherData($voucherData);

    //     $this->assertTrue($result['success']);
    // }

    // /** - Khoảng cách ngày hợp lệ 1 tháng */
    // public function test_one_month_duration_returns_success()
    // {
    //     $startDate = Carbon::today()->format('Y-m-d');
    //     $endDate = Carbon::today()->addMonth()->format('Y-m-d');

    //     $voucherData = $this->getValidVoucherData([
    //         'NgayBatDau' => $startDate,
    //         'NgayKetThuc' => $endDate
    //     ]);

    //     $result = $this->voucherService->validateVoucherData($voucherData);

    //     $this->assertTrue($result['success']);
    // }

    // /** - Xử lý cuối tháng và đầu tháng */
    // public function test_month_boundary_dates_return_success()
    // {
    //     // Sử dụng tháng trong tương lai
    //     $startDate = '2026-01-31'; // Ngày cuối tháng 1 năm 2026
    //     $endDate = '2026-02-01'; // Ngày đầu tháng 2 năm 2026

    //     $voucherData = $this->getValidVoucherData([
    //         'NgayBatDau' => $startDate,
    //         'NgayKetThuc' => $endDate
    //     ]);

    //     $result = $this->voucherService->validateVoucherData($voucherData);

    //     if (!$result['success']) {
    //         dump("Error: " . ($result['message'] ?? 'No message'));
    //     }

    //     $this->assertTrue($result['success']);
    // }