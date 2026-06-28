<?php

namespace App\Http\Controllers\Api\User;

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
     */
    public function getCheckoutInfo()
    {
        $user = Auth::user();
        
        $gioHang = GioHang::with(['chiTiet.monAn'])
            ->where('MaNguoiDung', $user->MaNguoiDung)
            ->first();

        if (!$gioHang || $gioHang->chiTiet->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Giỏ hàng trống'], 400);
        }

        $cartItems = $gioHang->chiTiet->map(function ($item) {
            $price = (float)($item->monAn->Gia ?? 0);
            return [
                'id'       => $item->MaMonAn,
                'name'     => $item->monAn->TenMonAn,
                'price'    => $price,
                'quantity' => $item->SoLuong,
                'image'    => $item->monAn->hinh_anh_url,
                'total'    => $item->SoLuong * $price
            ];
        });

        $subtotal = $cartItems->sum('total');
        $vouchers = GiamGia::available()->get()
            ->filter(fn($v) => $v->canUserUse($user->MaNguoiDung))
            ->map(function($v) {
            return [
                'code' => $v->MaCode,
                'percent' => $v->PhanTram,
                'min_order' => $v->DonHangToiThieu,
                'expires_at' => $v->NgayKetThuc,
                'loai_giam_gia' => $v->LoaiGiamGia,
                'giam_toi_da' => $v->GiamToiDa,
                'phan_tram_format' => $v->PhanTramFormat
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'cart_items' => $cartItems,
                'subtotal' => $subtotal,
                'vouchers' => $vouchers,
                'user_info' => ['name' => $user->HoTen, 'phone' => $user->SoDienThoai, 'email' => $user->Email]
            ]
        ]);
    }

    /**
     * Áp dụng voucher
     */
    public function applyVoucher(Request $request)
    {
        $code = trim($request->input('voucher_code'));
        $gioHang = GioHang::with(['chiTiet.monAn'])->where('MaNguoiDung', Auth::id())->first();
        if (!$gioHang) return response()->json(['success' => false, 'message' => 'Lỗi'], 400);

        $subtotal = $gioHang->chiTiet->sum(fn($ct) => $ct->SoLuong * (float)($ct->monAn->Gia ?? 0));
        $voucher = GiamGia::where('MaCode', $code)->available()->first();
        
        if (!$voucher) return response()->json(['success' => false, 'message' => 'Mã không hợp lệ, hết hạn hoặc đã hết số lượng sử dụng'], 400);
        if (!$voucher->canUserUse(Auth::id())) {
            return response()->json(['success' => false, 'message' => 'Bạn đã sử dụng mã giảm giá này tối đa số lần cho phép'], 400);
        }
        if ($voucher->DonHangToiThieu && $subtotal < $voucher->DonHangToiThieu) {
            return response()->json(['success' => false, 'message' => 'Chưa đủ điều kiện'], 400);
        }

        $discountValue = (int)$voucher->calculateDiscount($subtotal);

        return response()->json([
            'success' => true,
            'data' => [
                'voucher_code' => $voucher->MaCode,
                'voucher_id' => $voucher->MaGiamGia,
                'discount' => $discountValue,
                'total' => max(0, $subtotal - $discountValue),
                'subtotal' => $subtotal
            ]
        ]);
    }

    /**
     * Tạo đơn hàng (Tách đơn theo Nhà hàng)
     */
    public function createOrder(Request $request)
    {
        $user = Auth::user();
        $gioHang = GioHang::with(['chiTiet.monAn'])->where('MaNguoiDung', $user->MaNguoiDung)->first();
        if (!$gioHang || $gioHang->chiTiet->isEmpty()) return response()->json(['success' => false, 'message' => 'Trống'], 400);

        $paymentMethod = $request->input('payment_method');
        $voucherId = $request->input('voucher_id');

        $itemsByRestaurant = $gioHang->chiTiet->groupBy(function($ct) {
            return $ct->monAn->MaNhaHang;
        });

        try {
            DB::beginTransaction();
            $createdOrderIds = [];

            $subtotal = $gioHang->chiTiet->sum(fn($ct) => $ct->SoLuong * (float)($ct->monAn->Gia ?? 0));
            $effectivePercent = 0;
            if ($voucherId) {
                $voucher = GiamGia::where('MaGiamGia', $voucherId)->available()->first();
                if (!$voucher || !$voucher->canUserUse($user->MaNguoiDung)) {
                    return response()->json(['success' => false, 'message' => 'Mã giảm giá đã hết lượt sử dụng, đã hết hạn hoặc đã đạt giới hạn sử dụng trên tài khoản của bạn.'], 400);
                }
                $totalDiscount = (int)$voucher->calculateDiscount($subtotal);
                $effectivePercent = $subtotal > 0 ? ($totalDiscount / $subtotal) * 100 : 0;
            }

            foreach ($itemsByRestaurant as $maNhaHang => $items) {
                $resSubtotal = $items->sum(fn($ct) => $ct->SoLuong * (float)($ct->monAn->Gia ?? 0));
                $resDiscount = (int)floor($resSubtotal * $effectivePercent / 100);
                $resTotal = max(0, (int)$resSubtotal - $resDiscount);

                $donHang = DonHang::create([
                    'MaNguoiDung' => $user->MaNguoiDung,
                    'MaGiamGia' => $voucherId,
                    'TongTien' => $resTotal,
                    'PhuongThucThanhToan' => $paymentMethod,
                    'TrangThai' => 'Chờ xử lý',
                    'TenKhachHang' => $request->input('name'),
                    'SoDienThoai' => $request->input('phone'),
                    'DiaChiGiaoHang' => $request->input('address'),
                    'GhiChu' => $request->input('note')
                ]);

                foreach ($items as $ct) {
                    DonHangChiTiet::create([
                        'MaDonHang' => $donHang->MaDonHang,
                        'MaMonAn' => $ct->MaMonAn,
                        'SoLuong' => $ct->SoLuong,
                        'Gia' => (float)$ct->monAn->Gia
                    ]);
                }

                ThanhToan::create([
                    'MaDonHang'   => $donHang->MaDonHang,
                    'PhuongThuc'  => $paymentMethod,
                    'SoTien'      => $resTotal,
                    'TrangThai'   => 'Chờ thanh toán',
                    'NgayThanhToan' => now(),
                ]);

                $createdOrderIds[] = $donHang->MaDonHang;
            }

            if (count($createdOrderIds) > 0) {
                $groupPaymentCode = count($createdOrderIds) > 1 
                    ? 'TAFOOD' . implode('v', $createdOrderIds)
                    : 'TAFOOD' . $createdOrderIds[0];

                ThanhToan::whereIn('MaDonHang', $createdOrderIds)->update([
                    'MaGiaoDich' => $groupPaymentCode
                ]);
            }

            $gioHang->chiTiet()->delete();
            $gioHang->delete();

            // Tăng số lượng sử dụng của voucher
            if ($voucherId) {
                $voucher = GiamGia::find($voucherId);
                if ($voucher) {
                    $voucher->incrementUsage();
                }
            }

            DB::commit();

            return response()->json(['success' => true, 'data' => ['order_id' => $createdOrderIds[0], 'order_ids' => $createdOrderIds]], 201);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * PHỤC HỒI: Xử lý thanh toán online
     */
    public function processPayment(Request $request, $orderId)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Vui lòng đăng nhập'], 401);
        }

        $orderIds = explode(',', $orderId);
        $donHangs = DonHang::with('thanhToan')
            ->whereIn('MaDonHang', $orderIds)
            ->where('MaNguoiDung', $user->MaNguoiDung)
            ->get();

        if ($donHangs->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy đơn hàng'], 404);
        }

        $paymentMethod = $request->input('payment_method', 'Online');

        try {
            DB::beginTransaction();
            foreach ($donHangs as $donHang) {
                $groupPaymentCode = count($orderIds) > 1 ? 'TAFOOD' . implode('v', $orderIds) : 'TAFOOD' . $orderIds[0];
                if ($donHang->thanhToan) {
                    $donHang->thanhToan->update([
                        'PhuongThuc' => $paymentMethod,
                        'TrangThai' => 'Đã thanh toán',
                        'NgayThanhToan' => now(),
                        'MaGiaoDich' => $groupPaymentCode
                    ]);
                } else {
                    ThanhToan::create([
                        'MaDonHang' => $donHang->MaDonHang,
                        'PhuongThuc' => $paymentMethod,
                        'SoTien' => $donHang->TongTien,
                        'TrangThai' => 'Đã thanh toán',
                        'NgayThanhToan' => now(),
                        'MaGiaoDich' => $groupPaymentCode
                    ]);
                }
                $donHang->update([
                    'PhuongThucThanhToan' => $paymentMethod,
                    'TrangThai' => 'Đã xác nhận'
                ]);
            }
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Thanh toán thành công!',
                'data' => ['redirect_url' => route('checkout.payment-confirmation', $orderId)]
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function getOrder($orderId)
    {
        $donHang = DonHang::with(['chiTiet.monAn', 'thanhToan'])->find($orderId);
        if (!$donHang) return response()->json(['success' => false], 404);
        return response()->json(['success' => true, 'data' => ['order_id' => $donHang->MaDonHang, 'status' => $donHang->TrangThai, 'total_amount' => $donHang->TongTien]]);
    }

    public function getOrderHistory()
    {
        $orders = DonHang::where('MaNguoiDung', Auth::id())->orderBy('created_at', 'desc')->paginate(10);
        return response()->json([
            'success' => true,
            'data' => $orders->map(fn($o) => ['order_id' => $o->MaDonHang, 'total_amount' => $o->TongTien, 'status' => $o->TrangThai, 'created_at' => $o->created_at->format('Y-m-d H:i:s')])
        ]);
    }
}

