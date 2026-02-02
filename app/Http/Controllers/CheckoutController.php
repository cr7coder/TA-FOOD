<?php

namespace App\Http\Controllers;

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
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để thanh toán');
        }

        $gioHang = GioHang::with(['chiTiet.monAn'])
            ->where('MaNguoiDung', Auth::user()->MaNguoiDung)
            ->first();

        $cartItems = [];
        $subtotal = 0;

        if ($gioHang && $gioHang->chiTiet->count() > 0) {
            $cartItems = $gioHang->chiTiet->map(function ($item) {
                $price = (float)($item->monAn->Gia ?? 0);
                $itemTotal = $item->SoLuong * $price;
                return [
                    'id'       => $item->MaMonAn,
                    'name'     => $item->monAn->TenMonAn,
                    'price'    => $price,
                    'quantity' => $item->SoLuong,
                    'image'    => $item->monAn->HinhAnh ? asset('images/' . $item->monAn->HinhAnh) : asset('images/no-image.png'),
                    'total'    => $itemTotal
                ];
            })->toArray();

            $subtotal = collect($cartItems)->sum('total');
        }

        if (empty($cartItems)) {
            return redirect()->route('foods.index')->with('error', 'Giỏ hàng trống');
        }

        $vouchers = GiamGia::active()->get();

        // Tự động áp dụng voucher tốt nhất
        $bestVoucherApplied = $this->autoApplyBestVoucher($subtotal);

        // Debug
        logger()->info('Checkout Debug', [
            'subtotal' => $subtotal,
            'vouchers_count' => $vouchers->count(),
            'best_voucher_applied' => $bestVoucherApplied
        ]);

        return view('checkout.index', compact('cartItems', 'subtotal', 'vouchers', 'bestVoucherApplied'));
    }

    // Áp dụng voucher (đã gửi trước đó)
    public function applyVoucher(Request $request)
    {
        $request->validate([
            'voucher_code' => 'required|string'
        ]);

        $code = trim($request->input('voucher_code'));

        $gioHang = GioHang::with(['chiTiet.monAn'])
            ->where('MaNguoiDung', Auth::user()->MaNguoiDung)
            ->first();

        if (!$gioHang || $gioHang->chiTiet->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Giỏ hàng trống'], 422);
        }

        $subtotal = $gioHang->chiTiet->sum(fn($ct) => $ct->SoLuong * (float)($ct->monAn->Gia ?? 0));

        $voucher = GiamGia::where('MaCode', $code)->active()->first();
        if (!$voucher) {
            return response()->json(['success' => false, 'message' => 'Mã giảm giá không hợp lệ/hết hạn'], 422);
        }

        $percent       = (float)$voucher->PhanTram;
        $discountValue = (int) floor($subtotal * $percent / 100);
        $totalValue    = max(0, (int)$subtotal - $discountValue);

        session([
            'checkout.voucher' => [
                'code'     => $voucher->MaCode,
                'percent'  => $percent,
                'discount' => $discountValue,
                'total'    => $totalValue,
                'MaGiamGia' => $voucher->MaGiamGia,
            ],
        ]);

        return response()->json([
            'success' => true,
            'message' => "Áp dụng mã {$voucher->MaCode} (-{$percent}%) thành công",
            'voucher' => [
                'code'           => $voucher->MaCode,
                'percent'        => $percent,
                'discount'       => number_format($discountValue, 0, ',', '.') . ' đ',
                'total'          => number_format($totalValue, 0, ',', '.') . ' đ',
                'discount_value' => $discountValue,
                'total_value'    => $totalValue,
            ],
        ]);
    }

    // Lấy voucher tốt nhất tự động
    public function getBestVoucher(Request $request)
    {
        $gioHang = GioHang::with(['chiTiet.monAn'])
            ->where('MaNguoiDung', Auth::user()->MaNguoiDung)
            ->first();

        if (!$gioHang || $gioHang->chiTiet->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Giỏ hàng trống'], 422);
        }

        $subtotal = $gioHang->chiTiet->sum(fn($ct) => $ct->SoLuong * (float)($ct->monAn->Gia ?? 0));

        // Lấy tất cả voucher khả dụng
        $vouchers = GiamGia::active()
            ->where(function ($query) use ($subtotal) {
                $query->whereNull('DonHangToiThieu')
                    ->orWhere('DonHangToiThieu', '<=', $subtotal);
            })
            ->get();

        if ($vouchers->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Không có mã giảm giá khả dụng'], 422);
        }

        // Tìm voucher có giá trị giảm cao nhất
        $bestVoucher = null;
        $maxDiscount = 0;

        foreach ($vouchers as $voucher) {
            $percent = (float)$voucher->PhanTram;
            $discountValue = (int) floor($subtotal * $percent / 100);

            if ($discountValue > $maxDiscount) {
                $maxDiscount = $discountValue;
                $bestVoucher = $voucher;
            }
        }

        if (!$bestVoucher) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy voucher phù hợp'], 422);
        }

        // Áp dụng voucher tốt nhất
        $percent = (float)$bestVoucher->PhanTram;
        $discountValue = (int) floor($subtotal * $percent / 100);
        $totalValue = max(0, (int)$subtotal - $discountValue);

        session([
            'checkout.voucher' => [
                'code'     => $bestVoucher->MaCode,
                'percent'  => $percent,
                'discount' => $discountValue,
                'total'    => $totalValue,
                'MaGiamGia' => $bestVoucher->MaGiamGia,
            ],
        ]);

        return response()->json([
            'success' => true,
            'message' => "Đã tự động áp dụng mã {$bestVoucher->MaCode} (-{$percent}%) - Ưu đãi tốt nhất!",
            'voucher' => [
                'code'           => $bestVoucher->MaCode,
                'percent'        => $percent,
                'discount'       => number_format($discountValue, 0, ',', '.') . ' đ',
                'total'          => number_format($totalValue, 0, ',', '.') . ' đ',
                'discount_value' => $discountValue,
                'total_value'    => $totalValue,
            ],
        ]);
    }

    // Tự động áp dụng voucher tốt nhất khi load trang
    private function autoApplyBestVoucher($subtotal)
    {
        // Lấy tất cả voucher khả dụng
        $vouchers = GiamGia::active()
            ->where(function ($query) use ($subtotal) {
                $query->whereNull('DonHangToiThieu')
                    ->orWhere('DonHangToiThieu', '<=', $subtotal);
            })
            ->get();

        if ($vouchers->isEmpty()) {
            return null;
        }

        // Tìm voucher có giá trị giảm cao nhất
        $bestVoucher = null;
        $maxDiscount = 0;

        foreach ($vouchers as $voucher) {
            $percent = (float)$voucher->PhanTram;
            $discountValue = (int) floor($subtotal * $percent / 100);

            if ($discountValue > $maxDiscount) {
                $maxDiscount = $discountValue;
                $bestVoucher = $voucher;
            }
        }

        if (!$bestVoucher) {
            return null;
        }

        // Áp dụng voucher tốt nhất vào session
        $percent = (float)$bestVoucher->PhanTram;
        $discountValue = (int) floor($subtotal * $percent / 100);
        $totalValue = max(0, (int)$subtotal - $discountValue);

        session([
            'checkout.voucher' => [
                'code'     => $bestVoucher->MaCode,
                'percent'  => $percent,
                'discount' => $discountValue,
                'total'    => $totalValue,
                'MaGiamGia' => $bestVoucher->MaGiamGia,
            ],
        ]);

        return [
            'code'           => $bestVoucher->MaCode,
            'percent'        => $percent,
            'discount'       => number_format($discountValue, 0, ',', '.') . ' đ',
            'total'          => number_format($totalValue, 0, ',', '.') . ' đ',
            'discount_value' => $discountValue,
            'total_value'    => $totalValue,
        ];
    }

    // Lưu đơn + chi tiết + thanh toán (giả lập)
    public function store(Request $request)
    {
        $request->validate([
            'customer_info.name'    => 'required|string|max:255',
            'customer_info.phone'   => 'required|string|max:20',
            'customer_info.address' => 'required|string|max:1000',
            'payment_method'        => 'required|in:COD,Online',
        ]);

        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập');
        }

        $gioHang = GioHang::with(['chiTiet.monAn'])
            ->where('MaNguoiDung', $user->MaNguoiDung)
            ->first();

        if (!$gioHang || $gioHang->chiTiet->isEmpty()) {
            return redirect()->route('foods.index')->with('error', 'Giỏ hàng trống');
        }

        // Tính subtotal từ giỏ
        $subtotal = $gioHang->chiTiet->sum(fn($ct) => $ct->SoLuong * (float)($ct->monAn->Gia ?? 0));

        // Áp dụng voucher từ session (nếu có)
        $appliedVoucher = session('checkout.voucher');
        $maGiamGia      = $appliedVoucher['MaGiamGia'] ?? null;
        $percent        = (float)($appliedVoucher['percent'] ?? 0);
        $discountValue  = (int) floor($subtotal * $percent / 100);
        $tongTien       = max(0, (int)$subtotal - $discountValue);

        $paymentMethod = $request->input('payment_method'); // COD | Online
        $info          = $request->input('customer_info', []);
        $ghiChu        = $info['note'] ?? null;

        try {
            DB::beginTransaction();

            // 1) Tạo đơn hàng
            $donHang = DonHang::create([
                'MaNguoiDung'          => $user->MaNguoiDung,
                'MaGiamGia'            => $maGiamGia,
                'TongTien'             => $tongTien,
                'PhuongThucThanhToan'  => $paymentMethod,
                'TrangThai'            => $paymentMethod === 'COD' ? 'Đã thanh toán' : 'Chờ xử lý',
                'TenKhachHang'         => $info['name'],
                'SoDienThoai'          => $info['phone'],
                'DiaChiGiaoHang'       => $info['address'],
                'GhiChu'               => $ghiChu,
            ]);

            // 2) Tạo chi tiết đơn
            foreach ($gioHang->chiTiet as $ct) {
                $price = (float)($ct->monAn->Gia ?? 0);
                DonHangChiTiet::create([
                    'MaDonHang' => $donHang->MaDonHang,
                    'MaMonAn'   => $ct->MaMonAn,
                    'SoLuong'   => $ct->SoLuong,
                    'Gia'       => $price, // chụp moment giá
                ]);
            }

            // 3) Tạo thanh toán (giả lập)
            ThanhToan::create([
                'MaDonHang'   => $donHang->MaDonHang,
                'PhuongThuc'  => $paymentMethod,         // COD | Online
                'SoTien'      => $tongTien,
                'TrangThai'   => $paymentMethod === 'COD' ? 'Thành công' : 'Chờ xử lý',
                'NgayThanhToan' => now(),
            ]);

            // Xóa giỏ hàng
            $gioHang->chiTiet()->delete();
            $gioHang->delete();

            // Xóa voucher trong session
            session()->forget('checkout.voucher');

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);
            return back()->withErrors('Không thể tạo đơn hàng. Vui lòng thử lại.')->withInput();
        }

        // Điều hướng
        if ($paymentMethod === 'Online') {
            // Hiển thị trang mô phỏng thanh toán online
            return redirect()->route('checkout.payment', $donHang->MaDonHang);
        }

        // COD: đã đánh dấu thành công → tới trang success
        return redirect()->route('checkout.success', $donHang->MaDonHang)
            ->with('success', 'Đặt hàng thành công!');
    }

    // Trang chọn phương thức online (mô phỏng)
    public function payment($maDonHang)
    {
        $donHang = DonHang::with(['chiTiet.monAn', 'thanhToan', 'giamGia'])->findOrFail($maDonHang);
        return view('checkout.payment', compact('donHang'));
    }

    // Xử lý mô phỏng thanh toán online
    public function processPayment(Request $request, $maDonHang)
    {
        $donHang = DonHang::with('thanhToan')->findOrFail($maDonHang);

        $simulateSuccess = (bool)$request->boolean('simulate_success', true);

        try {
            DB::beginTransaction();

            $thanhToan = $donHang->thanhToan ?: new ThanhToan([
                'MaDonHang'  => $donHang->MaDonHang,
                'PhuongThuc' => 'Online',
                'SoTien'     => $donHang->TongTien,
            ]);

            $thanhToan->TrangThai = $simulateSuccess ? 'Thành công' : 'Thất bại';
            $thanhToan->NgayThanhToan = now();
            $thanhToan->save();

            if ($simulateSuccess) {
                $donHang->TrangThai = 'Đã thanh toán';
                $donHang->save();
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);
            return back()->withErrors('Lỗi xử lý thanh toán. Vui lòng thử lại.');
        }

        if (!$simulateSuccess) {
            return back()->with('error', 'Thanh toán thất bại (mô phỏng). Hãy thử phương thức khác.');
        }

        return redirect()->route('checkout.success', $donHang->MaDonHang)
            ->with('success', 'Thanh toán thành công!');
    }

    // Trang thành công
    public function success($maDonHang)
    {
        $donHang = DonHang::with(['chiTiet.monAn', 'thanhToan', 'giamGia'])->findOrFail($maDonHang);
        return view('checkout.success', compact('donHang'));
    }
}
