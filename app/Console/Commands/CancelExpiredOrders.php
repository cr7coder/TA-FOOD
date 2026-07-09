<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\DonHang;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CancelExpiredOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:cancel-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tự động hủy các đơn hàng online chưa thanh toán sau 15 phút';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            DonHang::cancelExpiredOrders();
            $this->info("Đã chạy tự động quét hủy đơn hàng online quá hạn thanh toán.");
        } catch (\Exception $e) {
            Log::error("Lỗi khi chạy lệnh orders:cancel-expired: " . $e->getMessage());
            $this->error("Có lỗi xảy ra: " . $e->getMessage());
        }
    }
}
