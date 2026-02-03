<?php

namespace Tests\Unit\Voucher;

use Tests\TestCase;
use App\Services\VoucherService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StartDateTest extends TestCase
{
    use RefreshDatabase;
    protected $voucherService;
    protected function setUp(): void
    {
        parent::setUp();
        $this->voucherService = new VoucherService();
    }
    public function test_8e9_start_date_empty_returns_error()
    {
        $voucherData = $this->getValidVoucherData(['NgayBatDau' => '']);

        $result = $this->voucherService->validateVoucherData($voucherData);

        $this->assertFalse($result['success']);
        $this->assertEquals('Ngày bắt đầu không được bỏ trống (8E.9)', $result['message']);
    }
    public function test_8e10_start_date_invalid_format_returns_error()
    {
        $invalidDates = [
            '2024-13-01',      // Tháng không hợp lệ
            '2024-02-30',      // Ngày không tồn tại
            '2024/01/15',      // Sai format
            '15-01-2024',      // Sai format
            'invalid-date',    // Không phải ngày
            '2024-1-1',        // Thiếu số 0
            '24-01-01'         // Năm 2 chữ số
        ];

        foreach ($invalidDates as $date) {
            $voucherData = $this->getValidVoucherData(['NgayBatDau' => $date]);
            $result = $this->voucherService->validateVoucherData($voucherData);

            $this->assertFalse($result['success'], "Date '{$date}' should be invalid");
            $this->assertEquals('Ngày bắt đầu không hợp lệ (8E.10)', $result['message']);
        }
    }
    public function test_8e11_start_date_must_be_today_or_future()
    {
        $pastDates = [
            Carbon::yesterday()->format('Y-m-d'),
            Carbon::now()->subDays(7)->format('Y-m-d'),
            Carbon::now()->subMonths(1)->format('Y-m-d'),
            '2020-01-01'
        ];
        foreach ($pastDates as $date) {
            $voucherData = $this->getValidVoucherData(['NgayBatDau' => $date]);
            $result = $this->voucherService->validateVoucherData($voucherData);

            $this->assertFalse($result['success'], "Date '{$date}' should be invalid");
            $this->assertEquals('Ngày bắt đầu phải lớn hơn hoặc bằng ngày hiện tại (8E.11)', $result['message']);
        }
    }
    // Test thành công với các ngày bắt đầu hợp lệ
    public function test_comprehensive_valid_start_dates_returns_success()
    {
        $validDates = [
            Carbon::today()->format('Y-m-d'),           // Hôm nay
            Carbon::tomorrow()->format('Y-m-d'),        // Ngày mai
            Carbon::now()->addDays(3)->format('Y-m-d'), // 3 ngày sau
            Carbon::now()->addWeek()->format('Y-m-d'),  // 1 tuần sau
            Carbon::now()->addMonth()->format('Y-m-d'), // 1 tháng sau
            Carbon::now()->addYear()->format('Y-m-d'),  // 1 năm sau
            '2026-12-31',                                // Cuối năm sau
            '2028-02-29'                                 // Năm nhuận
        ];

        foreach ($validDates as $date) {
            $voucherData = $this->getValidVoucherData(['NgayBatDau' => $date]);
            $result = $this->voucherService->validateVoucherData($voucherData);

            $this->assertTrue($result['success'], "Start date '{$date}' should be valid");
        }
    }
    private function getValidVoucherData($override = [])
    {
        $baseData = [
            'MaCode' => 'SALE2024',
            'PhanTram' => 15.50,
            'NgayBatDau' => Carbon::today()->format('Y-m-d'),
            'NgayKetThuc' => Carbon::today()->addDays(7)->format('Y-m-d')
        ];

        $data = array_merge($baseData, $override);

        // Đảm bảo NgayKetThuc luôn sau NgayBatDau (chỉ khi NgayBatDau là valid date)
        if (isset($override['NgayBatDau']) && !isset($override['NgayKetThuc'])) {
            try {
                $startDate = Carbon::parse($data['NgayBatDau']);
                $data['NgayKetThuc'] = $startDate->addDays(7)->format('Y-m-d');
            } catch (\Exception $e) {
                // Giữ nguyên NgayKetThuc default nếu NgayBatDau invalid
                $data['NgayKetThuc'] = $baseData['NgayKetThuc'];
            }
        }

        return $data;
    }
}




    // /** - Ngày bắt đầu hợp lệ với các format khác nhau */
    // public function test_valid_start_dates_return_success()
    // {
    //     $validDates = [
    //         Carbon::today()->format('Y-m-d'),           // Hôm nay
    //         Carbon::tomorrow()->format('Y-m-d'),        // Ngày mai
    //         Carbon::now()->addDays(7)->format('Y-m-d'), // 1 tuần sau
    //         Carbon::now()->addMonths(1)->format('Y-m-d'), // 1 tháng sau
    //         '2025-12-31'                                // Cuối năm sau
    //     ];

    //     foreach ($validDates as $date) {
    //         $voucherData = $this->getValidVoucherData(['NgayBatDau' => $date]);
    //         $result = $this->voucherService->validateVoucherData($voucherData);

    //         if (!$result['success']) {
    //             dump("Date: {$date}, Error: " . ($result['message'] ?? 'No message'));
    //         }

    //         $this->assertTrue($result['success'], "Date '{$date}' should be valid");
    //     }
    // }

    // /** - Ngày bắt đầu = hôm nay */
    // public function test_start_date_today_returns_success()
    // {
    //     $today = Carbon::today()->format('Y-m-d');
    //     $voucherData = $this->getValidVoucherData(['NgayBatDau' => $today]);

    //     $result = $this->voucherService->validateVoucherData($voucherData);

    //     $this->assertTrue($result['success']);
    // }

    // /** - Ngày bắt đầu trong tương lai gần */
    // public function test_start_date_near_future_returns_success()
    // {
    //     $tomorrow = Carbon::tomorrow()->format('Y-m-d');
    //     $voucherData = $this->getValidVoucherData(['NgayBatDau' => $tomorrow]);

    //     $result = $this->voucherService->validateVoucherData($voucherData);

    //     $this->assertTrue($result['success']);
    // }

    // /** - Ngày bắt đầu trong tương lai xa */
    // public function test_start_date_far_future_returns_success()
    // {
    //     $futureDate = Carbon::now()->addYears(1)->format('Y-m-d');
    //     $voucherData = $this->getValidVoucherData(['NgayBatDau' => $futureDate]);

    //     $result = $this->voucherService->validateVoucherData($voucherData);

    //     $this->assertTrue($result['success']);
    // }

    // /** - Ngày nhuận hợp lệ */
    // public function test_leap_year_date_returns_success()
    // {
    //     $leapYearDate = '2028-02-29'; // 2028 là năm nhuận trong tương lai
    //     $voucherData = $this->getValidVoucherData(['NgayBatDau' => $leapYearDate]);

    //     $result = $this->voucherService->validateVoucherData($voucherData);

    //     $this->assertTrue($result['success']);
    // }