<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\DonHang;
use App\Models\ThanhToan;

class PaymentController extends Controller
{
    /**
     * Lấy danh sách thanh toán của user
     * GET /api/v1/payments
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $user = Auth::user();

        $payments = ThanhToan::with(['donHang' => function($query) use ($user) {
                $query->where('MaNguoiDung', $user->MaNguoiDung);
            }])
            ->whereHas('donHang', function($query) use ($user) {
                $query->where('MaNguoiDung', $user->MaNguoiDung);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $payments->map(function($payment) {
                return [
                    'payment_id' => $payment->MaThanhToan,
                    'order_id' => $payment->MaDonHang,
                    'amount' => $payment->SoTien,
                    'method' => $payment->PhuongThuc,
                    'status' => $payment->TrangThai,
                    'transaction_code' => $payment->MaGiaoDich,
                    'created_at' => $payment->created_at->format('Y-m-d H:i:s'),
                    'updated_at' => $payment->updated_at->format('Y-m-d H:i:s')
                ];
            }),
            'pagination' => [
                'current_page' => $payments->currentPage(),
                'total_pages' => $payments->lastPage(),
                'total_items' => $payments->total(),
                'per_page' => $payments->perPage()
            ]
        ]);
    }

    /**
     * Lấy thông tin chi tiết một thanh toán
     * GET /api/v1/payments/{id}
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $user = Auth::user();

        $payment = ThanhToan::with(['donHang.chiTiet.monAn', 'donHang.giamGia'])
            ->whereHas('donHang', function($query) use ($user) {
                $query->where('MaNguoiDung', $user->MaNguoiDung);
            })
            ->find($id);

        if (!$payment) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy thanh toán'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'payment_id' => $payment->MaThanhToan,
                'order_id' => $payment->MaDonHang,
                'amount' => $payment->SoTien,
                'method' => $payment->PhuongThuc,
                'status' => $payment->TrangThai,
                'transaction_code' => $payment->MaGiaoDich,
                'order' => [
                    'customer_name' => $payment->donHang->TenKhachHang,
                    'phone' => $payment->donHang->SoDienThoai,
                    'address' => $payment->donHang->DiaChiGiaoHang,
                    'total_amount' => $payment->donHang->TongTien,
                    'status' => $payment->donHang->TrangThai,
                    'items' => $payment->donHang->chiTiet->map(function($item) {
                        return [
                            'food_name' => $item->monAn->TenMonAn,
                            'quantity' => $item->SoLuong,
                            'price' => $item->Gia,
                            'total' => $item->SoLuong * $item->Gia
                        ];
                    })
                ],
                'created_at' => $payment->created_at->format('Y-m-d H:i:s')
            ]
        ]);
    }

    /**
     * Xử lý thanh toán cho đơn hàng
     * POST /api/v1/payments
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:don_hang,MaDonHang',
            'payment_method' => 'required|in:COD,Online,MoMo,ZaloPay,VNPay',
            'success' => 'nullable|boolean' // Dùng để mô phỏng kết quả thanh toán online
        ]);

        $user = Auth::user();
        $orderId = $request->input('order_id');
        $paymentMethod = $request->input('payment_method');
        $simulateSuccess = $request->boolean('success', true); // Mặc định là thành công

        $donHang = DonHang::where('MaDonHang', $orderId)
            ->where('MaNguoiDung', $user->MaNguoiDung)
            ->first();

        if (!$donHang) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy đơn hàng'
            ], 404);
        }

        // Kiểm tra xem đã có thanh toán chưa
        $existingPayment = ThanhToan::where('MaDonHang', $orderId)->first();
        if ($existingPayment && $existingPayment->TrangThai === 'Đã thanh toán') {
            return response()->json([
                'success' => false,
                'message' => 'Đơn hàng đã được thanh toán'
            ], 400);
        }

        try {
            DB::beginTransaction();

            // Xác định trạng thái thanh toán
            $paymentStatus = 'Chờ thanh toán';
            $orderStatus = 'Chờ xử lý';

            if ($paymentMethod === 'COD') {
                $paymentStatus = 'Đã thanh toán';
                $orderStatus = 'Đã thanh toán';
            } elseif ($simulateSuccess) {
                $paymentStatus = 'Đã thanh toán';
                $orderStatus = 'Đã thanh toán';
            } else {
                $paymentStatus = 'Thất bại';
                $orderStatus = 'Chờ xử lý';
            }

            // Cập nhật hoặc tạo thanh toán
            if ($existingPayment) {
                $existingPayment->update([
                    'PhuongThuc' => $paymentMethod,
                    'TrangThai' => $paymentStatus
                ]);
                $thanhToan = $existingPayment;
            } else {
                $thanhToan = ThanhToan::create([
                    'MaDonHang' => $orderId,
                    'PhuongThuc' => $paymentMethod,
                    'SoTien' => $donHang->TongTien,
                    'TrangThai' => $paymentStatus
                ]);
            }

            // Cập nhật đơn hàng
            $donHang->update([
                'PhuongThucThanhToan' => $paymentMethod,
                'TrangThai' => $orderStatus
            ]);

            DB::commit();

            // Trả về status code phù hợp với kết quả
            $statusCode = $paymentStatus === 'Đã thanh toán' ? 201 : 400;

            return response()->json([
                'success' => $paymentStatus === 'Đã thanh toán',
                'message' => $paymentStatus === 'Đã thanh toán' ? 'Thanh toán thành công' : 'Thanh toán thất bại',
                'data' => [
                    'payment_id' => $thanhToan->MaThanhToan,
                    'order_id' => $orderId,
                    'amount' => $thanhToan->SoTien,
                    'method' => $paymentMethod,
                    'status' => $paymentStatus,
                    'order_status' => $orderStatus,
                    'created_at' => $thanhToan->created_at->format('Y-m-d H:i:s'),
                    'updated_at' => $thanhToan->updated_at->format('Y-m-d H:i:s')
                ]
            ], $statusCode);

        } catch (\Throwable $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Lỗi xử lý thanh toán',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Cập nhật trạng thái thanh toán (dùng cho callback từ cổng thanh toán)
     * PUT /api/v1/payments/{id}
     * 
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Chờ thanh toán,Đã thanh toán,Thất bại,Đã hoàn tiền'
        ]);

        $user = Auth::user();

        $payment = ThanhToan::whereHas('donHang', function($query) use ($user) {
                $query->where('MaNguoiDung', $user->MaNguoiDung);
            })
            ->find($id);

        if (!$payment) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy thanh toán'
            ], 404);
        }

        $newStatus = $request->input('status');

        try {
            DB::beginTransaction();

            $payment->update([
                'TrangThai' => $newStatus
            ]);

            // Cập nhật trạng thái đơn hàng
            $donHang = $payment->donHang;
            if ($newStatus === 'Đã thanh toán') {
                $donHang->update(['TrangThai' => 'Đã thanh toán']);
            } elseif ($newStatus === 'Thất bại') {
                $donHang->update(['TrangThai' => 'Chờ xử lý']);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật trạng thái thanh toán thành công',
                'data' => [
                    'payment_id' => $payment->MaThanhToan,
                    'status' => $payment->TrangThai,
                    'order_status' => $donHang->TrangThai
                ]
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Lỗi cập nhật thanh toán',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Kiểm tra trạng thái thanh toán
     * GET /api/v1/payments/check/{orderId}
     * 
     * @param int $orderId
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkPaymentStatus($orderId)
    {
        $user = Auth::user();

        $donHang = DonHang::with('thanhToan')
            ->where('MaDonHang', $orderId)
            ->where('MaNguoiDung', $user->MaNguoiDung)
            ->first();

        if (!$donHang) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy đơn hàng'
            ], 404);
        }

        $payment = $donHang->thanhToan;

        return response()->json([
            'success' => true,
            'data' => [
                'order_id' => $orderId,
                'payment_exists' => $payment !== null,
                'payment_id' => $payment?->MaThanhToan,
                'payment_status' => $payment?->TrangThai ?? 'Chưa có thanh toán',
                'payment_method' => $payment?->PhuongThuc,
                'amount' => $payment?->SoTien,
                'order_status' => $donHang->TrangThai,
                'is_paid' => $payment?->TrangThai === 'Đã thanh toán'
            ]
        ]);
    }
}
