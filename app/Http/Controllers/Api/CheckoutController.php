<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\GioHang;
use App\Models\GiamGia;
use App\Models\DonHang;
use App\Models\DonHangChiTiet;
use App\Models\ThanhToan;

class CheckoutController extends Controller
{
    /**
     * Lấy thông tin checkout (giỏ hàng + vouchers)
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCheckoutInfo()
    {
        $user = Auth::user();
        
        $gioHang = GioHang::with(['chiTiet.monAn'])
            ->where('MaNguoiDung', $user->MaNguoiDung)
            ->first();

        if (!$gioHang || $gioHang->chiTiet->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Giỏ hàng trống'
            ], 400);
        }

        $cartItems = $gioHang->chiTiet->map(function ($item) {
            $price = (float)($item->monAn->Gia ?? 0);
            return [
                'id'       => $item->MaMonAn,
                'name'     => $item->monAn->TenMonAn,
                'price'    => $price,
                'quantity' => $item->SoLuong,
                'image'    => $item->monAn->HinhAnh ? url('images/' . $item->monAn->HinhAnh) : null,
                'total'    => $item->SoLuong * $price
            ];
        });

        $subtotal = $cartItems->sum('total');
        
        $vouchers = GiamGia::active()
            ->where(function ($query) use ($subtotal) {
                $query->whereNull('DonHangToiThieu')
                    ->orWhere('DonHangToiThieu', '<=', $subtotal);
            })
            ->get()
            ->map(function($v) {
                return [
                    'code' => $v->MaCode,
                    'percent' => $v->PhanTram,
                    'min_order' => $v->DonHangToiThieu,
                    'expires_at' => $v->NgayKetThuc
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'cart_items' => $cartItems,
                'subtotal' => $subtotal,
                'vouchers' => $vouchers,
                'user_info' => [
                    'name' => $user->HoTen,
                    'phone' => $user->SoDienThoai,
                    'email' => $user->Email
                ]
            ]
        ]);
    }

    /**
     * Áp dụng voucher
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function applyVoucher(Request $request)
    {
        $request->validate([
            'voucher_code' => 'required|string'
        ]);

        $user = Auth::user();
        $code = trim($request->input('voucher_code'));

        $gioHang = GioHang::with(['chiTiet.monAn'])
            ->where('MaNguoiDung', $user->MaNguoiDung)
            ->first();

        if (!$gioHang || $gioHang->chiTiet->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Giỏ hàng trống'
            ], 400);
        }

        $subtotal = $gioHang->chiTiet->sum(fn($ct) => $ct->SoLuong * (float)($ct->monAn->Gia ?? 0));

        $voucher = GiamGia::where('MaCode', $code)->active()->first();
        
        if (!$voucher) {
            return response()->json([
                'success' => false,
                'message' => 'Mã giảm giá không hợp lệ hoặc đã hết hạn'
            ], 400);
        }

        if ($voucher->DonHangToiThieu && $subtotal < $voucher->DonHangToiThieu) {
            return response()->json([
                'success' => false,
                'message' => "Đơn hàng tối thiểu " . number_format($voucher->DonHangToiThieu) . "đ để sử dụng mã này"
            ], 400);
        }

        $percent = (float)$voucher->PhanTram;
        $discountValue = (int)floor($subtotal * $percent / 100);
        $totalValue = max(0, (int)$subtotal - $discountValue);

        return response()->json([
            'success' => true,
            'message' => "Áp dụng mã {$voucher->MaCode} (-{$percent}%) thành công",
            'data' => [
                'voucher_code' => $voucher->MaCode,
                'voucher_id' => $voucher->MaGiamGia,
                'percent' => $percent,
                'discount' => $discountValue,
                'total' => $totalValue,
                'subtotal' => $subtotal
            ]
        ]);
    }

    /**
     * Tạo đơn hàng
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createOrder(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:1000',
            'payment_method' => 'required|in:COD,Online,MoMo,ZaloPay,VNPay',
            'voucher_id' => 'nullable|exists:giam_gia,MaGiamGia',
            'note' => 'nullable|string|max:500'
        ]);

        $user = Auth::user();

        $gioHang = GioHang::with(['chiTiet.monAn'])
            ->where('MaNguoiDung', $user->MaNguoiDung)
            ->first();

        if (!$gioHang || $gioHang->chiTiet->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Giỏ hàng trống'
            ], 400);
        }

        $subtotal = $gioHang->chiTiet->sum(fn($ct) => $ct->SoLuong * (float)($ct->monAn->Gia ?? 0));

        // Tính giảm giá nếu có
        $discountValue = 0;
        $voucherId = $request->input('voucher_id');
        
        if ($voucherId) {
            $voucher = GiamGia::find($voucherId);
            if ($voucher && $voucher->isActive()) {
                $percent = (float)$voucher->PhanTram;
                $discountValue = (int)floor($subtotal * $percent / 100);
            }
        }

        $tongTien = max(0, (int)$subtotal - $discountValue);
        $paymentMethod = $request->input('payment_method');

        try {
            DB::beginTransaction();

            // 1) Tạo đơn hàng
            $donHang = DonHang::create([
                'MaNguoiDung' => $user->MaNguoiDung,
                'MaGiamGia' => $voucherId,
                'TongTien' => $tongTien,
                'PhuongThucThanhToan' => $paymentMethod,
                'TrangThai' => $paymentMethod === 'COD' ? 'Đã thanh toán' : 'Chờ xử lý',
                'TenKhachHang' => $request->input('name'),
                'SoDienThoai' => $request->input('phone'),
                'DiaChiGiaoHang' => $request->input('address'),
                'GhiChu' => $request->input('note')
            ]);

            // 2) Tạo chi tiết đơn
            foreach ($gioHang->chiTiet as $ct) {
                $price = (float)($ct->monAn->Gia ?? 0);
                DonHangChiTiet::create([
                    'MaDonHang' => $donHang->MaDonHang,
                    'MaMonAn' => $ct->MaMonAn,
                    'SoLuong' => $ct->SoLuong,
                    'Gia' => $price
                ]);
            }

            // 3) Tạo thanh toán
            $thanhToan = ThanhToan::create([
                'MaDonHang' => $donHang->MaDonHang,
                'PhuongThuc' => $paymentMethod,
                'SoTien' => $tongTien,
                'TrangThai' => $paymentMethod === 'COD' ? 'Đã thanh toán' : 'Chờ thanh toán',
                'NgayThanhToan' => now()
            ]);

            // 4) Xóa giỏ hàng
            $gioHang->chiTiet()->delete();
            $gioHang->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Đặt hàng thành công',
                'data' => [
                    'order_id' => $donHang->MaDonHang,
                    'total_amount' => $tongTien,
                    'payment_method' => $paymentMethod,
                    'status' => $donHang->TrangThai,
                    'payment_url' => $paymentMethod !== 'COD' ? url("/api/v1/checkout/payment/{$donHang->MaDonHang}") : null
                ]
            ], 201);

        } catch (\Throwable $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Không thể tạo đơn hàng',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Lấy thông tin đơn hàng
     * 
     * @param int $orderId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getOrder($orderId)
    {
        $user = Auth::user();

        $donHang = DonHang::with(['chiTiet.monAn', 'giamGia', 'thanhToan'])
            ->where('MaDonHang', $orderId)
            ->where('MaNguoiDung', $user->MaNguoiDung)
            ->first();

        if (!$donHang) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy đơn hàng'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'order_id' => $donHang->MaDonHang,
                'customer_name' => $donHang->TenKhachHang,
                'phone' => $donHang->SoDienThoai,
                'address' => $donHang->DiaChiGiaoHang,
                'note' => $donHang->GhiChu,
                'total_amount' => $donHang->TongTien,
                'status' => $donHang->TrangThai,
                'payment_method' => $donHang->PhuongThucThanhToan,
                'payment_status' => $donHang->thanhToan?->TrangThai,
                'voucher' => $donHang->giamGia ? [
                    'code' => $donHang->giamGia->MaCode,
                    'percent' => $donHang->giamGia->PhanTram
                ] : null,
                'items' => $donHang->chiTiet->map(function($item) {
                    return [
                        'food_name' => $item->monAn->TenMonAn,
                        'quantity' => $item->SoLuong,
                        'price' => $item->Gia,
                        'total' => $item->SoLuong * $item->Gia
                    ];
                }),
                'created_at' => $donHang->created_at->format('Y-m-d H:i:s')
            ]
        ]);
    }

    /**
     * Xử lý thanh toán online (mô phỏng)
     * 
     * @param Request $request
     * @param int $orderId
     * @return \Illuminate\Http\JsonResponse
     */
    public function processPayment(Request $request, $orderId)
    {
        $request->validate([
            'success' => 'required|boolean'
        ]);

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

        $simulateSuccess = $request->boolean('success');

        try {
            DB::beginTransaction();

            $thanhToan = $donHang->thanhToan;
            if ($thanhToan) {
                $thanhToan->TrangThai = $simulateSuccess ? 'Đã thanh toán' : 'Thất bại';
                $thanhToan->NgayThanhToan = now();
                $thanhToan->save();
            }

            if ($simulateSuccess) {
                $donHang->TrangThai = 'Đã thanh toán';
                $donHang->save();
            }

            DB::commit();

            return response()->json([
                'success' => $simulateSuccess,
                'message' => $simulateSuccess ? 'Thanh toán thành công' : 'Thanh toán thất bại',
                'data' => [
                    'order_id' => $donHang->MaDonHang,
                    'payment_status' => $thanhToan?->TrangThai,
                    'order_status' => $donHang->TrangThai
                ]
            ]);

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
     * Lấy lịch sử đơn hàng
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getOrderHistory()
    {
        $user = Auth::user();

        $orders = DonHang::with(['chiTiet.monAn', 'thanhToan'])
            ->where('MaNguoiDung', $user->MaNguoiDung)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $orders->map(function($order) {
                return [
                    'order_id' => $order->MaDonHang,
                    'total_amount' => $order->TongTien,
                    'status' => $order->TrangThai,
                    'payment_method' => $order->PhuongThucThanhToan,
                    'payment_status' => $order->thanhToan?->TrangThai,
                    'items_count' => $order->chiTiet->count(),
                    'created_at' => $order->created_at->format('Y-m-d H:i:s')
                ];
            }),
            'pagination' => [
                'current_page' => $orders->currentPage(),
                'total_pages' => $orders->lastPage(),
                'total_items' => $orders->total(),
                'per_page' => $orders->perPage()
            ]
        ]);
    }
}
