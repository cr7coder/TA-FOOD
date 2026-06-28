<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Lên lịch tự động hủy các đơn hàng online quá hạn thanh toán mỗi 5 phút
Schedule::command('orders:cancel-expired')->everyFiveMinutes();
