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
        $expiredTime = Carbon::now()->subMinutes(15);

        // Lấy các đơn hàng thanh toán online, đang chờ xử lý và tạo quá 15 phút
        $expiredOrders = DonHang::where('PhuongThucThanhToan', 'Online')
            ->where('TrangThai', 'Chờ xử lý')
            ->where('created_at', '<=', $expiredTime)
            ->get();

        $count = 0;
        foreach ($expiredOrders as $order) {
            // Hủy đơn hàng
            $order->update([
                'TrangThai' => 'Hủy',
                'ly_do_huy' => 'Hệ thống tự động hủy do quá hạn thanh toán',
                'nguoi_huy' => 'system'
            ]);

            // Hủy trạng thái thanh toán và khóa link PayOS
            if ($order->thanhToan) {
                if ($order->thanhToan->payos_order_code && in_array($order->thanhToan->TrangThai, ['Chờ thanh toán', 'Thất bại'])) {
                    $payOSService = app(\App\Services\PayOSService::class);
                    $payOSService->cancelPaymentLink((int)$order->thanhToan->payos_order_code, 'Quá hạn 15 phút không thanh toán');
                }

                $order->thanhToan->update([
                    'TrangThai' => 'Thất bại'
                ]);
            }
            
            $count++;
        }

        if ($count > 0) {
            Log::info("Đã tự động hủy {$count} đơn hàng online quá hạn thanh toán.");
            $this->info("Đã tự động hủy {$count} đơn hàng online quá hạn thanh toán.");
        } else {
            $this->info("Không có đơn hàng nào quá hạn.");
        }
    }
}
