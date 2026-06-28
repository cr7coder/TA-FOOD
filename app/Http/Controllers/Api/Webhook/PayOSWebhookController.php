<?php

namespace App\Http\Controllers\Api\Webhook;

use App\Http\Controllers\Controller;
use App\Models\DonHang;
use App\Models\ThanhToan;
use App\Services\PayOSService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PayOSWebhookController extends Controller
{
    protected PayOSService $payOSService;

    public function __construct(PayOSService $payOSService)
    {
        $this->payOSService = $payOSService;
    }

    /**
     * Nhận và xử lý webhook từ PayOS
     * POST /api/payos/webhook
     */
    public function handleWebhook(Request $request)
    {
        $payload = $request->all();

        Log::info('PayOS Webhook Received', [
            'payload' => $payload
        ]);

        // 1. Xác thực chữ ký webhook
        if (!$this->payOSService->verifyWebhook($payload)) {
            Log::error('PayOS Webhook Signature Invalid');
            return response()->json([
                'success' => false,
                'message' => 'Chữ ký không hợp lệ!'
            ], 400);
        }

        $data = $payload['data'];
        $orderCode = $data['orderCode'];
        $amount = $data['amount'];
        $reference = $data['reference'] ?? null; // Mã giao dịch của ngân hàng
        $code = $data['code']; // Mã trạng thái giao dịch (00 = thành công)

        // 2. Kiểm tra nếu giao dịch không thành công
        if ($code !== '00') {
            Log::warning('PayOS Webhook status is not success', [
                'code' => $code,
                'desc' => $data['desc'] ?? ''
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Giao dịch không thành công, bỏ qua.'
            ]);
        }

        try {
            DB::beginTransaction();

            // 3. Tìm các bản ghi ThanhToan có payos_order_code tương ứng
            $thanhToans = ThanhToan::where('payos_order_code', $orderCode)
                ->where('TrangThai', '!=', 'Đã thanh toán') // Chỉ xử lý thanh toán chưa hoàn thành
                ->get();

            if ($thanhToans->isEmpty()) {
                DB::rollBack();
                Log::info('PayOS Webhook: No pending payment records found for orderCode: ' . $orderCode);
                return response()->json([
                    'success' => true,
                    'message' => 'Đã xử lý hoặc không tìm thấy bản ghi thanh toán.'
                ]);
            }

            $orderIds = $thanhToans->pluck('MaDonHang')->toArray();
            $donHangs = DonHang::whereIn('MaDonHang', $orderIds)
                ->where('TrangThai', 'Chờ xử lý')
                ->with(['chiTiet.monAn.nhaHang'])
                ->get();

            // 4. Cập nhật trạng thái thanh toán và đơn hàng
            foreach ($thanhToans as $thanhToan) {
                $thanhToan->TrangThai = 'Đã thanh toán';
                $thanhToan->NgayThanhToan = now();
                if ($reference) {
                    $thanhToan->MaGiaoDich = $reference; // Cập nhật mã giao dịch của ngân hàng
                }
                $thanhToan->save();
            }

            foreach ($donHangs as $donHang) {
                $donHang->TrangThai = 'Đã xác nhận';
                $donHang->XacNhanAt = now();
                $donHang->save();

                $orderCodeFmt = 'ORD' . str_pad($donHang->MaDonHang, 5, '0', STR_PAD_LEFT);

                // 5. Gửi thông báo đến Khách hàng
                NotificationService::add(
                    $donHang->MaNguoiDung,
                    '✅ Thanh toán online thành công!',
                    "Hệ thống đã ghi nhận thanh toán thành công cho đơn hàng {$orderCodeFmt}. Nhà hàng đang chuẩn bị món ăn cho bạn! 🍳",
                    $donHang->MaDonHang
                );

                // 6. Gửi thông báo đến Nhà hàng (Seller)
                $nhaHang = $donHang->chiTiet->first()?->monAn?->nhaHang;
                if ($nhaHang) {
                    NotificationService::add(
                        $nhaHang->MaNguoiDung,
                        '🆕 Đơn hàng đã thanh toán online!',
                        "Đơn hàng {$orderCodeFmt} đã thanh toán thành công. Hãy bắt tay vào chuẩn bị món ngay nhé!",
                        $donHang->MaDonHang
                    );
                }
            }

            DB::commit();
            Log::info('PayOS Webhook processed successfully for orderCode: ' . $orderCode);

            return response()->json([
                'success' => true,
                'message' => 'Xử lý webhook thành công.'
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('PayOS Webhook processing failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Lỗi máy chủ nội bộ.'
            ], 500);
        }
    }
}
