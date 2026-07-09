<?php

namespace App\Http\Controllers\Web\Client;

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
    public function index(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để thanh toán');
        }

        $gioHang = GioHang::with(['chiTiet.monAn.nhaHang'])
            ->where('MaNguoiDung', Auth::user()->MaNguoiDung)
            ->first();

        $selectedItemsStr = $request->query('items');
        $selectedIds = $selectedItemsStr ? explode(',', $selectedItemsStr) : null;

        $cartItems = [];
        $subtotal = 0;
        $defaultShippingFee = 0;

        if ($gioHang && $gioHang->chiTiet->count() > 0) {
            $chiTietFiltered = $gioHang->chiTiet;
            if ($selectedIds) {
                $chiTietFiltered = $chiTietFiltered->filter(fn($item) => in_array($item->MaMonAn, $selectedIds));
            }

            $cartItems = $chiTietFiltered->map(function ($item) {
                $price = (float)($item->monAn->Gia ?? 0);
                $itemTotal = $item->SoLuong * $price;
                return [
                    'id'       => $item->MaMonAn,
                    'name'     => $item->monAn->TenMonAn,
                    'price'    => $price,
                    'quantity' => $item->SoLuong,
                    'image'    => $item->monAn->hinh_anh_url,
                    'total'    => $itemTotal
                ];
            })->toArray();

            $subtotal = collect($cartItems)->sum('total');

            // Tính toán phí vận chuyển mặc định (không có tọa độ, dựa trên phí ship cơ bản của từng nhà hàng)
            $itemsByRestaurant = $chiTietFiltered->groupBy(function($ct) {
                return $ct->monAn->MaNhaHang;
            });
            foreach ($itemsByRestaurant as $maNhaHang => $items) {
                $mon = $items->first()->monAn;
                $nhaHang = $mon ? $mon->nhaHang : null;
                if ($nhaHang) {
                    $defaultShippingFee += (float)($nhaHang->phi_ship_co_ban ?? 15000);
                }
            }
        }

        if (empty($cartItems)) {
            $hasPendingOrder = DonHang::where('MaNguoiDung', Auth::user()->MaNguoiDung)
                ->where('PhuongThucThanhToan', 'Online')
                ->where('TrangThai', 'Chờ xử lý')
                ->whereHas('thanhToan', function($q) {
                    $q->where('TrangThai', 'Chờ thanh toán');
                })
                ->exists();
            if ($hasPendingOrder) {
                return redirect()->route('orders.history')->with('warning', 'Bạn đang có đơn hàng chờ thanh toán. Hãy kiểm tra và thanh toán nhé!');
            }
            return redirect()->route('foods.index')->with('error', 'Giỏ hàng trống');
        }

        $vouchers = GiamGia::available()->get()->filter(fn($v) => $v->canUserUse(Auth::id()));

        // Calculate suggested best voucher based on current subtotal
        $suggestedBestVoucher = null;
        $maxDiscount = 0;
        foreach ($vouchers as $voucher) {
            if ($voucher->DonHangToiThieu === null || $subtotal >= $voucher->DonHangToiThieu) {
                $discountValue = (int)$voucher->calculateDiscount($subtotal);
                if ($discountValue > $maxDiscount) {
                    $maxDiscount = $discountValue;
                    $suggestedBestVoucher = $voucher;
                }
            }
        }

        $bestVoucherApplied = null;
        $preAppliedError = null;

        // Priority 1: User explicitly selected a voucher from homepage (pre_applied_code)
        $preAppliedCode = session('checkout.pre_applied_code');
        if ($preAppliedCode) {
            $preAppliedVoucher = GiamGia::where('MaCode', $preAppliedCode)->available()->first();
            if ($preAppliedVoucher && !$preAppliedVoucher->canUserUse(Auth::id())) {
                $preAppliedVoucher = null;
                session()->forget('checkout.pre_applied_code');
                session()->flash('error', "Mã giảm giá {$preAppliedCode} đã đạt giới hạn sử dụng trên tài khoản của bạn.");
            }
            if ($preAppliedVoucher) {
                if ($preAppliedVoucher->DonHangToiThieu === null || $subtotal >= $preAppliedVoucher->DonHangToiThieu) {
                    $discountValue = (int)$preAppliedVoucher->calculateDiscount($subtotal);
                    $percent = $subtotal > 0 ? ($discountValue / $subtotal) * 100 : 0;
                    $totalValue = max(0, (int)$subtotal - $discountValue);
     
                    session([
                        'checkout.voucher' => [
                            'code'      => $preAppliedVoucher->MaCode,
                            'percent'   => $percent,
                            'discount'  => $discountValue,
                            'total'     => $totalValue,
                            'MaGiamGia' => $preAppliedVoucher->MaGiamGia,
                        ],
                    ]);
     
                    $bestVoucherApplied = [
                        'code'           => $preAppliedVoucher->MaCode,
                        'percent'        => $percent,
                        'discount'       => number_format($discountValue, 0, ',', '.') . ' đ',
                        'total'          => number_format($totalValue, 0, ',', '.') . ' đ',
                        'discount_value' => $discountValue,
                        'total_value'    => $totalValue,
                        'loai_giam_gia'  => $preAppliedVoucher->LoaiGiamGia,
                        'display_discount' => $preAppliedVoucher->LoaiGiamGia === 'TienMat' ? number_format($preAppliedVoucher->GiamToiDa, 0, ',', '.') . 'đ' : $preAppliedVoucher->PhanTram . '%'
                    ];
     
                    // Forget pre-applied code now that it is successfully applied
                    session()->forget('checkout.pre_applied_code');
                } else {
                    // Do NOT forget the pre-applied code! Keep it so that if they add more items it auto-applies.
                    if (session('checkout.pre_applied_show_warning')) {
                        $diff = $preAppliedVoucher->DonHangToiThieu - $subtotal;
                        $preAppliedError = [
                            'code' => $preAppliedVoucher->MaCode,
                            'min_order' => $preAppliedVoucher->DonHangToiThieu,
                            'diff' => $diff,
                            'discount_desc' => $preAppliedVoucher->LoaiGiamGia === 'TienMat' 
                                ? number_format($preAppliedVoucher->GiamToiDa, 0, ',', '.') . 'đ' 
                                : $preAppliedVoucher->PhanTram . '%',
                        ];
                        session()->forget('checkout.pre_applied_show_warning');
                    }
                }
            }
        }
 
        // Priority 2: There's already a voucher in session from a previous manual apply — keep it
        if (!$bestVoucherApplied && session()->has('checkout.voucher')) {
            $existingVoucher = session('checkout.voucher');
            $existingCode = $existingVoucher['code'] ?? null;
 
            if ($existingCode) {
                $voucherModel = GiamGia::where('MaCode', $existingCode)->available()->first();
                if ($voucherModel && !$voucherModel->canUserUse(Auth::id())) {
                    $voucherModel = null;
                    session()->forget('checkout.voucher');
                    session()->flash('error', "Mã giảm giá {$existingCode} đã đạt giới hạn sử dụng trên tài khoản của bạn.");
                }
                if ($voucherModel && ($voucherModel->DonHangToiThieu === null || $subtotal >= $voucherModel->DonHangToiThieu)) {
                    // Recalculate with current subtotal (in case cart changed)
                    $discountValue = (int)$voucherModel->calculateDiscount($subtotal);
                    $percent = $subtotal > 0 ? ($discountValue / $subtotal) * 100 : 0;
                    $totalValue = max(0, (int)$subtotal - $discountValue);
 
                    session([
                        'checkout.voucher' => [
                            'code'      => $voucherModel->MaCode,
                            'percent'   => $percent,
                            'discount'  => $discountValue,
                            'total'     => $totalValue,
                            'MaGiamGia' => $voucherModel->MaGiamGia,
                        ],
                    ]);
 
                    $bestVoucherApplied = [
                        'code'           => $voucherModel->MaCode,
                        'percent'        => $percent,
                        'discount'       => number_format($discountValue, 0, ',', '.') . ' đ',
                        'total'          => number_format($totalValue, 0, ',', '.') . ' đ',
                        'discount_value' => $discountValue,
                        'total_value'    => $totalValue,
                        'loai_giam_gia'  => $voucherModel->LoaiGiamGia,
                        'display_discount' => $voucherModel->LoaiGiamGia === 'TienMat' ? number_format($voucherModel->GiamToiDa, 0, ',', '.') . 'đ' : $voucherModel->PhanTram . '%'
                    ];
                } else {
                    // Voucher no longer valid — clear it
                    // If it was because of min order, let's demote it to a pre-applied code so it keeps being saved!
                    if ($voucherModel && $subtotal < $voucherModel->DonHangToiThieu) {
                        session(['checkout.pre_applied_code' => $voucherModel->MaCode]);
                        session(['checkout.pre_applied_show_warning' => true]);
                        $diff = $voucherModel->DonHangToiThieu - $subtotal;
                        $preAppliedError = [
                            'code' => $voucherModel->MaCode,
                            'min_order' => $voucherModel->DonHangToiThieu,
                            'diff' => $diff,
                            'discount_desc' => $voucherModel->LoaiGiamGia === 'TienMat' 
                                ? number_format($voucherModel->GiamToiDa, 0, ',', '.') . 'đ' 
                                : $voucherModel->PhanTram . '%',
                        ];
                        session()->forget('checkout.pre_applied_show_warning');
                    }
                    session()->forget('checkout.voucher');
                }
            }
        }
 
        // Priority 3: No voucher at all → auto-apply the best available one
        // (skip if user explicitly removed a voucher this session)
        if (!$bestVoucherApplied && !session('checkout.no_auto_voucher')) {
            $bestVoucherApplied = $this->autoApplyBestVoucher($subtotal);
        }
 
        // When a pre_applied_code is successfully applied, also clear the no_auto_voucher flag
        // (already cleared above, but ensure consistency)
        if ($bestVoucherApplied && session('checkout.no_auto_voucher')) {
            session()->forget('checkout.no_auto_voucher');
        }
 
        // Debug
        logger()->info('Checkout Debug', [
            'subtotal' => $subtotal,
            'vouchers_count' => $vouchers->count(),
            'best_voucher_applied' => $bestVoucherApplied
        ]);
 
        return view('client.checkout.index', compact('cartItems', 'subtotal', 'vouchers', 'bestVoucherApplied', 'suggestedBestVoucher', 'defaultShippingFee', 'preAppliedError'));
    }

    // Lưu mã giảm giá đã chọn từ trang chủ vào Session (gọi qua AJAX)
    public function preApplyVoucher(Request $request)
    {
        $request->validate([
            'voucher_code' => 'required|string'
        ]);

        $code = trim($request->input('voucher_code'));

        $voucher = GiamGia::where('MaCode', $code)->active()->first();
        if (!$voucher) {
            return response()->json(['success' => false, 'message' => 'Mã giảm giá không tồn tại hoặc đã hết hạn'], 422);
        }

        session(['checkout.pre_applied_code' => $voucher->MaCode]);
        session(['checkout.pre_applied_show_warning' => true]);

        return response()->json([
            'success' => true,
            'message' => "Đã lưu mã {$voucher->MaCode}! Mã sẽ tự động kích hoạt khi bạn thanh toán."
        ]);
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

        $voucher = GiamGia::where('MaCode', $code)->available()->first();
        if (!$voucher) {
            return response()->json(['success' => false, 'message' => 'Mã giảm giá không hợp lệ, hết hạn hoặc đã hết số lượng sử dụng'], 422);
        }
        if (!$voucher->canUserUse(Auth::id())) {
            return response()->json(['success' => false, 'message' => 'Bạn đã sử dụng mã giảm giá này tối đa số lần cho phép'], 422);
        }

        $discountValue = (int)$voucher->calculateDiscount($subtotal);
        $percent       = $subtotal > 0 ? ($discountValue / $subtotal) * 100 : 0;
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
        // Clear no_auto_voucher flag since user just explicitly applied a new code
        session()->forget('checkout.no_auto_voucher');

        $discountDisplay = $voucher->LoaiGiamGia === 'TienMat' ? number_format($discountValue, 0, ',', '.') . 'đ' : $voucher->PhanTram . '%';

        return response()->json([
            'success' => true,
            'message' => "Áp dụng mã {$voucher->MaCode} (-{$discountDisplay}) thành công",
            'voucher' => [
                           'code'           => $voucher->MaCode,
                           'percent'        => $percent,
                           'discount'       => number_format($discountValue, 0, ',', '.') . ' đ',
                           'total'          => number_format($totalValue, 0, ',', '.') . ' đ',
                           'discount_value' => $discountValue,
                           'total_value'    => $totalValue,
                           'loai_giam_gia'  => $voucher->LoaiGiamGia,
                           'display_discount' => $discountDisplay
                       ],
        ]);
    }

    // Hủy áp dụng voucher (xóa khỏi session)
    public function removeVoucher(Request $request)
    {
        session()->forget('checkout.voucher');
        session()->forget('checkout.pre_applied_code');
        // Flag to prevent auto-reapplying vouchers after the user explicitly removed one
        session(['checkout.no_auto_voucher' => true]);
        return response()->json([
            'success' => true,
            'message' => 'Đã hủy áp dụng mã giảm giá thành công'
        ]);
    }

    // Tính phí vận chuyển dựa trên khoảng cách (Haversine formula) và lưu vào session
    public function calculateShipping(Request $request)
    {
        $latCustomer = $request->input('lat');
        $lngCustomer = $request->input('lng');

        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Vui lòng đăng nhập'], 401);
        }

        $gioHang = GioHang::with(['chiTiet.monAn.nhaHang'])
            ->where('MaNguoiDung', $user->MaNguoiDung)
            ->first();

        if (!$gioHang || $gioHang->chiTiet->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Giỏ hàng trống',
                'shipping_fee' => 0,
                'distance' => 0
            ]);
        }

        $subtotal = $gioHang->chiTiet->sum(fn($ct) => $ct->SoLuong * (float)($ct->monAn->Gia ?? 0));

        // After calculating subtotal, ensure any voucher stored in session still meets the minimum order requirement.
        if (session()->has('checkout.voucher')) {
            $sessionVoucher = session('checkout.voucher');
            $code = $sessionVoucher['code'] ?? null;
            if ($code) {
                $voucherModel = GiamGia::where('MaCode', $code)->available()->first();
                if (!$voucherModel || !$voucherModel->canUserUse(Auth::id()) || ($voucherModel->DonHangToiThieu !== null && $subtotal < $voucherModel->DonHangToiThieu)) {
                    // Voucher no longer applicable – remove it and notify user
                    session()->forget('checkout.voucher');
                    // Optionally set a flash message to inform the user
                    $reason = !$voucherModel ? 'đã hết số lượng sử dụng hoặc hết hạn' : 
                              (! $voucherModel->canUserUse(Auth::id()) ? 'đã đạt giới hạn sử dụng trên tài khoản của bạn' : 'không đủ điều kiện cho đơn hàng hiện tại');
                    session()->flash('error', "Mã giảm giá {$code} {$reason} và đã được gỡ bỏ.");
                }
            }
        }

        $vouchers = GiamGia::available()->get();

        // Gom các món theo nhà hàng để tính phí ship của từng nhà hàng
        $itemsByRestaurant = $gioHang->chiTiet->groupBy(function($ct) {
            return $ct->monAn->MaNhaHang;
        });

        $totalShippingFee = 0;
        $details = [];

        foreach ($itemsByRestaurant as $maNhaHang => $items) {
            $mon = $items->first()->monAn;
            $nhaHang = $mon ? $mon->nhaHang : null;
            if (!$nhaHang) continue;

            $phiCoBan = (float)($nhaHang->phi_ship_co_ban ?? 15000);
            $phiMoiKm = (float)($nhaHang->phi_ship_moi_km ?? 5000);
            $kmMienPhi = (float)($nhaHang->km_mien_phi ?? 2.0);
            
            $latRestaurant = (float)($nhaHang->latitude ?? 21.081827);
            $lngRestaurant = (float)($nhaHang->longitude ?? 105.842790);

            $distance = 0.0;
            $shippingFee = $phiCoBan; // Phí mặc định

            if ($latCustomer && $lngCustomer) {
                $distance = $this->getDistance($latRestaurant, $lngRestaurant, $latCustomer, $lngCustomer);

                // Tính phí ship
                if ($distance <= $kmMienPhi) {
                    $shippingFee = 0; // Miễn phí hoàn toàn vì khoảng cách quá nhỏ!
                } else {
                    // Phí cơ bản + (Số km phụ thêm * đơn giá)
                    $extraKm = $distance - $kmMienPhi;
                    $shippingFee = $phiCoBan + $extraKm * $phiMoiKm;
                }
            } else {
                // Nếu khách hàng chưa ghim vị trí, áp dụng phí cơ bản hoặc 0 tùy chọn
                $shippingFee = $phiCoBan;
            }

            $totalShippingFee += $shippingFee;

            $details[] = [
                'nha_hang' => $nhaHang->TenNhaHang,
                'distance' => $distance,
                'shipping_fee' => $shippingFee,
                'km_mien_phi' => $kmMienPhi,
            ];
        }

        // Lưu phí vận chuyển vào session
        session(['checkout.shipping_fee' => $totalShippingFee]);

        return response()->json([
            'success' => true,
            'shipping_fee' => $totalShippingFee,
            'shipping_fee_format' => number_format($totalShippingFee, 0, ',', '.') . ' đ',
            'details' => $details
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
        $vouchers = GiamGia::available()
            ->where(function ($query) use ($subtotal) {
                $query->whereNull('DonHangToiThieu')
                    ->orWhere('DonHangToiThieu', '<=', $subtotal);
            })
            ->get()
            ->filter(fn($v) => $v->canUserUse(Auth::id()));

        if ($vouchers->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Không có mã giảm giá khả dụng'], 422);
        }

        // Tìm voucher có giá trị giảm cao nhất
        $bestVoucher = null;
        $maxDiscount = 0;

        foreach ($vouchers as $voucher) {
            $discountValue = (int)$voucher->calculateDiscount($subtotal);

            if ($discountValue > $maxDiscount) {
                $maxDiscount = $discountValue;
                $bestVoucher = $voucher;
            }
        }

        if (!$bestVoucher) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy voucher phù hợp'], 422);
        }

        // Áp dụng voucher tốt nhất
        $discountValue = (int)$bestVoucher->calculateDiscount($subtotal);
        $percent = $subtotal > 0 ? ($discountValue / $subtotal) * 100 : 0;
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

        $discountDisplay = $bestVoucher->LoaiGiamGia === 'TienMat' ? number_format($discountValue, 0, ',', '.') . 'đ' : $bestVoucher->PhanTram . '%';

        return response()->json([
            'success' => true,
            'message' => "Đã tự động áp dụng mã {$bestVoucher->MaCode} (-{$discountDisplay}) - Ưu đãi tốt nhất!",
            'voucher' => [
                           'code'           => $bestVoucher->MaCode,
                           'percent'        => $percent,
                           'discount'       => number_format($discountValue, 0, ',', '.') . ' đ',
                           'total'          => number_format($totalValue, 0, ',', '.') . ' đ',
                           'discount_value' => $discountValue,
                           'total_value'    => $totalValue,
                           'loai_giam_gia'  => $bestVoucher->LoaiGiamGia,
                           'display_discount' => $discountDisplay
                       ],
        ]);
    }

    // Tự động áp dụng voucher tốt nhất khi load trang
    private function autoApplyBestVoucher($subtotal)
    {
        // Lấy tất cả voucher khả dụng
        $vouchers = GiamGia::available()
            ->where(function ($query) use ($subtotal) {
                $query->whereNull('DonHangToiThieu')
                    ->orWhere('DonHangToiThieu', '<=', $subtotal);
            })
            ->get()
            ->filter(fn($v) => $v->canUserUse(Auth::id()));

        if ($vouchers->isEmpty()) {
            return null;
        }

        // Tìm voucher có giá trị giảm cao nhất
        $bestVoucher = null;
        $maxDiscount = 0;

        foreach ($vouchers as $voucher) {
            $discountValue = (int)$voucher->calculateDiscount($subtotal);

            if ($discountValue > $maxDiscount) {
                $maxDiscount = $discountValue;
                $bestVoucher = $voucher;
            }
        }

        if (!$bestVoucher) {
            return null;
        }

        // Áp dụng voucher tốt nhất vào session
        $discountValue = (int)$bestVoucher->calculateDiscount($subtotal);
        $percent = $subtotal > 0 ? ($discountValue / $subtotal) * 100 : 0;
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
                  'loai_giam_gia'  => $bestVoucher->LoaiGiamGia,
                  'display_discount' => $bestVoucher->LoaiGiamGia === 'TienMat' ? number_format($bestVoucher->GiamToiDa, 0, ',', '.') . 'đ' : $bestVoucher->PhanTram . '%'
              ];
    }

    // Lưu đơn + chi tiết + thanh toán (VietQR hoặc COD)
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

        $selectedItemsStr = $request->input('selected_items');
        $selectedIds = $selectedItemsStr ? explode(',', $selectedItemsStr) : null;

        $gioHang = GioHang::with(['chiTiet.monAn'])
            ->where('MaNguoiDung', $user->MaNguoiDung)
            ->first();

        if (!$gioHang || $gioHang->chiTiet->isEmpty()) {
            return redirect()->route('orders.history')->with('error', 'Giỏ hàng của bạn đang trống. Vui lòng kiểm tra lại đơn hàng vừa tạo tại đây.');
        }

        $chiTietFiltered = $gioHang->chiTiet;
        if ($selectedIds) {
            $chiTietFiltered = $chiTietFiltered->filter(fn($ct) => in_array($ct->MaMonAn, $selectedIds));
        }

        if ($chiTietFiltered->isEmpty()) {
            return redirect()->route('orders.history')->with('error', 'Không tìm thấy sản phẩm nào được chọn để đặt hàng.');
        }

        $itemsByRestaurant = $chiTietFiltered->groupBy(function($ct) {
            return $ct->monAn->MaNhaHang;
        });

        // Áp dụng voucher từ session (nếu có)
        $appliedVoucher = session('checkout.voucher');
        $maGiamGia      = $appliedVoucher['MaGiamGia'] ?? null;
        $percent        = (float)($appliedVoucher['percent'] ?? 0);

        if ($maGiamGia) {
            $voucherModel = GiamGia::where('MaGiamGia', $maGiamGia)->available()->first();
            if (!$voucherModel || !$voucherModel->canUserUse($user->MaNguoiDung)) {
                session()->forget('checkout.voucher');
                $msg = (!$voucherModel) ? 'Mã giảm giá đã hết lượt sử dụng hoặc đã hết hạn. Vui lòng thử lại.' 
                                       : 'Bạn đã đạt giới hạn sử dụng mã giảm giá này trên tài khoản của mình.';
                return redirect()->back()->with('error', $msg);
            }
        }

        $paymentMethod = $request->input('payment_method'); // COD | Online
        $info          = $request->input('customer_info', []);
        $ghiChu        = $info['note'] ?? null;

        try {
            DB::beginTransaction();
            $createdOrderIds = [];

            $payosOrderCode = null;
            if ($paymentMethod === 'Online') {
                $payosOrderCode = intval(substr(time(), -7) . rand(10, 99));
            }

            foreach ($itemsByRestaurant as $maNhaHang => $items) {
                $resSubtotal = $items->sum(fn($ct) => $ct->SoLuong * (float)($ct->monAn->Gia ?? 0));
                $discountValue = (int) floor($resSubtotal * $percent / 100);
                
                // Tính toán phí vận chuyển riêng cho nhà hàng này dựa trên định vị thực tế
                $firstMon = $items->first()->monAn;
                $nhaHang = $firstMon ? $firstMon->nhaHang : null;
                $shippingFee = 0;
                
                if ($nhaHang) {
                    $phiCoBan = (float)($nhaHang->phi_ship_co_ban ?? 15000);
                    $phiMoiKm = (float)($nhaHang->phi_ship_moi_km ?? 5000);
                    $kmMienPhi = (float)($nhaHang->km_mien_phi ?? 2.0);
                    
                    $latRestaurant = (float)($nhaHang->latitude ?? 21.081827);
                    $lngRestaurant = (float)($nhaHang->longitude ?? 105.842790);
                    
                    $latCustomer = $info['lat'] ?? null;
                    $lngCustomer = $info['lng'] ?? null;
                    
                    $shippingFee = $phiCoBan; // mặc định
                    
                    if ($latCustomer && $lngCustomer) {
                        $distance = $this->getDistance($latRestaurant, $lngRestaurant, $latCustomer, $lngCustomer);
                        
                        if ($distance <= $kmMienPhi) {
                            $shippingFee = 0; // Miễn phí giao hàng trong phạm vi khuyến mãi
                        } else {
                            $extraKm = $distance - $kmMienPhi;
                            $shippingFee = $phiCoBan + $extraKm * $phiMoiKm;
                        }
                    }
                }

                $tongTien = max(0, (int)$resSubtotal - $discountValue + (int)$shippingFee);

                // 1) Tạo đơn hàng
                $donHang = DonHang::create([
                    'MaNguoiDung'          => $user->MaNguoiDung,
                    'MaGiamGia'            => $maGiamGia,
                    'PhiVanChuyen'         => $shippingFee,
                    'TongTien'             => $tongTien,
                    'PhuongThucThanhToan'  => $paymentMethod,
                    'TrangThai'            => 'Chờ xử lý',
                    'TenKhachHang'         => $info['name'],
                    'SoDienThoai'          => $info['phone'],
                    'DiaChiGiaoHang'       => $info['address'],
                    'GhiChu'               => $ghiChu,
                ]);

                // 2) Tạo chi tiết đơn
                foreach ($items as $ct) {
                    $price = (float)($ct->monAn->Gia ?? 0);
                    DonHangChiTiet::create([
                        'MaDonHang' => $donHang->MaDonHang,
                        'MaMonAn'   => $ct->MaMonAn,
                        'SoLuong'   => $ct->SoLuong,
                        'Gia'       => $price, // chụp moment giá
                    ]);
                }

                // 3) Tạo thanh toán
                $phuongThuc = ($paymentMethod === 'Online') ? 'VietQR' : 'COD';
                $orderCode  = 'TAFOOD' . $donHang->MaDonHang;
                ThanhToan::create([
                    'MaDonHang'        => $donHang->MaDonHang,
                    'PhuongThuc'       => $phuongThuc,
                    'SoTien'           => $tongTien,
                    'TrangThai'        => 'Chờ thanh toán',
                    'NgayThanhToan'    => now(),
                    'payos_order_code' => $payosOrderCode ?: $orderCode,
                ]);

                $createdOrderIds[] = $donHang->MaDonHang;
            }

            // Gắn mã giao dịch gộp chung cho các đơn hàng tách
            if (count($createdOrderIds) > 0) {
                $groupPaymentCode = count($createdOrderIds) > 1 
                    ? 'TAFOOD' . implode('v', $createdOrderIds)
                    : 'TAFOOD' . $createdOrderIds[0];

                ThanhToan::whereIn('MaDonHang', $createdOrderIds)->update([
                    'MaGiaoDich' => $groupPaymentCode
                ]);
            }

            // Xóa các món đã đặt mua khỏi giỏ hàng
            if (isset($selectedIds) && $selectedIds) {
                $gioHang->chiTiet()->whereIn('MaMonAn', $selectedIds)->delete();
                if ($gioHang->chiTiet()->count() === 0) {
                    $gioHang->delete();
                }
            } else {
                $gioHang->chiTiet()->delete();
                $gioHang->delete();
            }

            // Xóa voucher trong session
            session()->forget('checkout.voucher');

            // Tăng số lượng sử dụng của voucher
            if ($maGiamGia) {
                $voucher = GiamGia::find($maGiamGia);
                if ($voucher) {
                    $voucher->incrementUsage();
                }
            }

            // Lưu địa chỉ giao hàng mặc định nếu được yêu cầu
            if ($request->input('save_address_default') == '1' || $request->input('save_address_default') === true) {
                $user->update([
                    'DiaChi' => $info['address']
                ]);
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);
            // Hiển thị lỗi chi tiết để debug
            return back()->withErrors('Lỗi: ' . $e->getMessage())->withInput();
        }

        $orderIdsStr = implode(',', $createdOrderIds);

        // Điều hướng
        if ($paymentMethod === 'Online') {
            if ($payosOrderCode) {
                $totalAmount = 0;
                $donHangs = DonHang::whereIn('MaDonHang', $createdOrderIds)->get();
                foreach ($donHangs as $dh) {
                    $totalAmount += $dh->TongTien;
                }

                // Gọi PayOS Service tạo link thanh toán
                $payOSService = app(\App\Services\PayOSService::class);
                $payosData = [
                    'orderCode' => $payosOrderCode,
                    'amount' => (int)$totalAmount,
                    'description' => 'Thanh toan TAFOOD',
                    'cancelUrl' => route('checkout.payos-cancel', ['orderCode' => $payosOrderCode]),
                    'returnUrl' => route('checkout.payos-return', ['orderCode' => $payosOrderCode]),
                ];

                $paymentLink = $payOSService->createPaymentLink($payosData);
                if ($paymentLink && isset($paymentLink['checkoutUrl'])) {
                    return redirect()->away($paymentLink['checkoutUrl']);
                }

                logger()->error('Failed to create PayOS payment link for orderCode: ' . $payosOrderCode);
            }
            // Fallback: Hiển thị trang mô phỏng thanh toán online
            return redirect()->route('checkout.payment', $orderIdsStr)->with('error', 'Không thể khởi tạo thanh toán tự động qua PayOS. Bạn có thể thanh toán thủ công.');
        }

        // COD: đã đánh dấu thành công → tới trang success
        return redirect()->route('checkout.success', $orderIdsStr)
            ->with('success', 'Đặt hàng thành công!');
    }

    // Trang thanh toán VietQR
    public function payment($maDonHang)
    {
        $ids = explode(',', $maDonHang);
        $donHangs = DonHang::with(['chiTiet.monAn', 'thanhToan', 'giamGia'])->whereIn('MaDonHang', $ids)->get();
        if ($donHangs->isEmpty()) abort(404);

        // Tạo URL QR VietQR động với đúng số tiền và nội dung
        $totalAmount   = $donHangs->sum('TongTien');
        if (count($ids) > 1) {
            $orderCode = 'TAFOOD' . implode('v', $ids);
        } else {
            $orderCode = 'TAFOOD' . $donHangs->first()->MaDonHang;
        }
        $bankId        = config('app.bank_id', env('BANK_ID', 'TCB'));
        $accountNo     = env('BANK_ACCOUNT_NO', '311027072004');
        $accountName   = env('BANK_ACCOUNT_NAME', 'NGUYEN TUAN ANH');
        $template      = env('BANK_TEMPLATE', 'compact2');

        $qrUrl = "https://img.vietqr.io/image/{$bankId}-{$accountNo}-{$template}.jpg"
            . '?amount=' . (int)$totalAmount
            . '&addInfo=' . urlencode($orderCode)
            . '&accountName=' . urlencode($accountName);

        return view('client.checkout.payment', compact('donHangs', 'maDonHang', 'qrUrl', 'orderCode', 'accountNo', 'accountName', 'bankId', 'totalAmount'));
    }

    // Xử lý mô phỏng thanh toán online
    public function processPayment(Request $request, $maDonHang)
    {
        $ids = explode(',', $maDonHang);
        $donHangs = DonHang::with('thanhToan')->whereIn('MaDonHang', $ids)->get();
        if ($donHangs->isEmpty()) return back()->withErrors('Đơn hàng không tồn tại.');

        $simulateSuccess = (bool)$request->boolean('simulate_success', true);
        $paymentMethod = $request->input('payment_method', 'Online');

        try {
            DB::beginTransaction();

            foreach ($donHangs as $donHang) {
                $thanhToan = $donHang->thanhToan ?: new ThanhToan([
                    'MaDonHang'  => $donHang->MaDonHang,
                    'PhuongThuc' => $paymentMethod,
                    'SoTien'     => $donHang->TongTien,
                ]);

                $thanhToan->PhuongThuc = $paymentMethod;
                $thanhToan->TrangThai = $simulateSuccess ? 'Đã thanh toán' : 'Thất bại';
                $thanhToan->NgayThanhToan = now();
                $thanhToan->MaGiaoDich = count($ids) > 1 ? 'TAFOOD' . implode('v', $ids) : 'TAFOOD' . $ids[0];
                $thanhToan->save();

                if ($simulateSuccess) {
                    $donHang->TrangThai = 'Đã xác nhận';
                    $donHang->PhuongThucThanhToan = $paymentMethod;
                    $donHang->save();
                }
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

        return redirect()->route('checkout.success', $maDonHang)
            ->with('success', 'Thanh toán thành công!');
    }

    // Khách xác nhận đã chuyển khoản (AJAX)
    public function confirmTransfer(Request $request, $maDonHang)
    {
        $ids = explode(',', $maDonHang);

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'payment_proof' => 'required|image|mimes:jpg,jpeg,png|max:5120'
        ], [
            'payment_proof.required' => 'Vui lòng tải lên hình ảnh hóa đơn chuyển khoản.',
            'payment_proof.image' => 'File tải lên phải là hình ảnh.',
            'payment_proof.mimes' => 'Hình ảnh chỉ hỗ trợ định dạng: jpg, jpeg, png.',
            'payment_proof.max' => 'Kích thước hình ảnh không được vượt quá 5MB.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $proofPath = null;
            if ($request->hasFile('payment_proof')) {
                $file = $request->file('payment_proof');
                $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('images/payment_proofs'), $fileName);
                $proofPath = 'payment_proofs/' . $fileName;
            }

            foreach ($ids as $id) {
                $donHang = DonHang::find($id);
                if (!$donHang) continue;

                // Chỉ cập nhật trạng thái THANH TOÁN sang 'Chờ xác nhận'
                // KHÔNG đổi trạng thái đơn hàng — Admin sẽ xác nhận sau khi kiểm tra tài khoản
                $groupPaymentCode = count($ids) > 1 ? 'TAFOOD' . implode('v', $ids) : 'TAFOOD' . $ids[0];
                ThanhToan::where('MaDonHang', $id)->update([
                    'TrangThai'             => 'Chờ xác nhận',
                    'NgayThanhToan'         => now(),
                    'MaGiaoDich'            => $groupPaymentCode,
                    'minh_chung_thanh_toan' => $proofPath,
                ]);
            }
            DB::commit();

            // Gửi thông báo đến Admin để kiểm tra và xác nhận tiền vào
            $orderList = implode(', ', array_map(fn($id) => 'ORD' . str_pad($id, 5, '0', STR_PAD_LEFT), $ids));
            $admins = \App\Models\User::where('VaiTro', 'QuanTri')->get();
            foreach ($admins as $admin) {
                \App\Services\NotificationService::add(
                    $admin->MaNguoiDung,
                    '💳 Khách vừa chuyển khoản — Cần xác nhận!',
                    "Khách hàng báo đã chuyển khoản cho đơn hàng {$orderList}. Vui lòng kiểm tra tài khoản ngân hàng và xác nhận!",
                    $ids[0]
                );
            }

            return response()->json([
                'success' => true,
                'message' => 'Đã ghi nhận! Vui lòng chờ Admin xác nhận thanh toán trong ít phút.',
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Có lỗi xảy ra: ' . $e->getMessage()], 500);
        }
    }



    // Polling API — kiểm tra trạng thái thanh toán (AJAX)
    public function checkPaymentStatus($maDonHang)
    {
        $ids = explode(',', $maDonHang);
        $firstId = $ids[0];

        $thanhToan = ThanhToan::where('MaDonHang', $firstId)->first();
        $donHang   = DonHang::find($firstId);

        if (!$thanhToan || !$donHang) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy đơn hàng']);
        }

        $isPaid = in_array($thanhToan->TrangThai, ['Đã thanh toán']);
        $isConfirmed = in_array($thanhToan->TrangThai, ['Chờ xác nhận', 'Đã thanh toán']);

        return response()->json([
            'success'       => true,
            'status'        => $thanhToan->TrangThai,
            'order_status'  => $donHang->TrangThai,
            'is_paid'       => $isPaid,
            'is_confirmed'  => $isConfirmed,
        ]);
    }

    // Trang thành công
    public function success($maDonHang)
    {
        $ids = explode(',', $maDonHang);
        $donHangs = DonHang::with(['chiTiet.monAn', 'thanhToan', 'giamGia'])->whereIn('MaDonHang', $ids)->get();
        if ($donHangs->isEmpty()) abort(404);
        $totalSum = $donHangs->sum('TongTien');
        return view('client.checkout.success', compact('donHangs', 'totalSum'));
    }

    // Trang xác nhận thanh toán (COD và Online)
    public function paymentConfirmation($maDonHang)
    {
        $ids = explode(',', $maDonHang);
        $donHangs = DonHang::with(['chiTiet.monAn', 'thanhToan', 'giamGia'])->whereIn('MaDonHang', $ids)->get();
        if ($donHangs->isEmpty()) abort(404);
        return view('client.checkout.payment-cod', compact('donHangs', 'maDonHang'));
    }

    // Xử lý khi khách quay lại sau khi thanh toán thành công trên PayOS
    public function payosReturn(Request $request)
    {
        $payosOrderCode = $request->input('orderCode');
        $code = $request->input('code');
        $cancel = $request->input('cancel');
        
        // Tìm các thanh toán tương ứng
        $thanhToans = ThanhToan::where('payos_order_code', $payosOrderCode)->get();
        if ($thanhToans->isEmpty()) {
            return redirect()->route('foods.index')->with('error', 'Không tìm thấy thông tin thanh toán.');
        }

        $orderIds = $thanhToans->pluck('MaDonHang')->toArray();
        $orderIdsStr = implode(',', $orderIds);

        // Cập nhật trạng thái ngay lập tức nếu thành công (dành cho localhost khi không có webhook)
        if ($code === '00' && $cancel === 'false') {
            foreach ($thanhToans as $thanhToan) {
                if ($thanhToan->TrangThai !== 'Đã thanh toán') {
                    $thanhToan->TrangThai = 'Đã thanh toán';
                    $thanhToan->NgayThanhToan = now();
                    $thanhToan->MaGiaoDich = $request->input('id') ?? ('PAYOS_' . $payosOrderCode);
                    $thanhToan->save();

                    $donHang = $thanhToan->donHang;
                    if ($donHang) {
                        $donHang->TrangThai = 'Đã xác nhận';
                        $donHang->save();
                        
                        $orderCodeFmt = 'ORD' . str_pad($donHang->MaDonHang, 5, '0', STR_PAD_LEFT);

                        // Gửi thông báo đến Khách hàng
                        \App\Services\NotificationService::add(
                            $donHang->MaNguoiDung,
                            '✅ Thanh toán online thành công!',
                            "Hệ thống đã ghi nhận thanh toán thành công cho đơn hàng {$orderCodeFmt}. Nhà hàng đang chuẩn bị món ăn cho bạn! 🍳",
                            $donHang->MaDonHang
                        );

                        // Gửi thông báo đến Nhà hàng (Seller)
                        $nhaHang = $donHang->chiTiet->first()?->monAn?->nhaHang;
                        if ($nhaHang) {
                            \App\Services\NotificationService::add(
                                $nhaHang->MaNguoiDung,
                                '🆕 Đơn hàng đã thanh toán online!',
                                "Đơn hàng {$orderCodeFmt} đã thanh toán thành công. Hãy bắt tay vào chuẩn bị món ngay nhé!",
                                $donHang->MaDonHang
                            );
                        }
                    }
                }
            }
        }

        // Chuyển hướng sang trang success
        return redirect()->route('checkout.success', $orderIdsStr)
            ->with('success', 'Thanh toán qua PayOS thành công!');
    }

    // Xử lý khi khách bấm hủy trên trang thanh toán PayOS
    public function payosCancel(Request $request)
    {
        $payosOrderCode = $request->input('orderCode');

        // Tìm các thanh toán tương ứng
        $thanhToans = ThanhToan::where('payos_order_code', $payosOrderCode)->get();
        if ($thanhToans->isEmpty()) {
            return redirect()->route('foods.index')->with('error', 'Giao dịch thanh toán đã bị hủy.');
        }

        $message = 'Bạn đã hủy giao dịch thanh toán PayOS.';
        
        // Cập nhật trạng thái thanh toán thành Thất bại và hủy đơn hàng
        foreach ($thanhToans as $thanhToan) {
            $donHang = $thanhToan->donHang;
            
            // Nếu đơn đã bị hủy trước đó (bởi admin/seller hoặc auto)
            if ($donHang && $donHang->TrangThai === 'Hủy') {
                $message = 'Giao dịch thanh toán đã bị đóng. Lý do: ' . ($donHang->ly_do_huy ?? 'Đơn hàng đã bị hủy trước đó');
            } 
            // Nếu đơn vẫn đang chờ xử lý thì cập nhật là khách tự hủy
            elseif ($donHang && $donHang->TrangThai === 'Chờ xử lý') {
                $donHang->TrangThai = 'Hủy';
                $donHang->ly_do_huy = 'Khách hàng hủy thanh toán trên cổng PayOS';
                $donHang->nguoi_huy = 'customer';
                $donHang->save();
            }

            if ($thanhToan->TrangThai === 'Chờ thanh toán') {
                $thanhToan->TrangThai = 'Thất bại';
                $thanhToan->save();
            }
        }

        return redirect()->route('orders.history')->with('error', $message);
    }

    // Tiếp tục thanh toán PayOS (sử dụng lại mã GD cũ)
    public function resumePayOSPayment($maDonHang)
    {
        // Tự động kiểm tra và hủy các đơn hàng đã quá hạn 15 phút mà chưa thanh toán
        \App\Models\DonHang::cancelExpiredOrders();

        $order = DonHang::with('thanhToan')->where('MaDonHang', $maDonHang)->where('MaNguoiDung', Auth::id())->first();

        if (!$order) {
            return redirect()->route('orders.history')->with('error', 'Không tìm thấy đơn hàng.');
        }

        if ($order->PhuongThucThanhToan !== 'Online' || $order->TrangThai !== 'Chờ xử lý') {
            if ($order->TrangThai === 'Hủy' && $order->nguoi_huy === 'system') {
                return redirect()->route('orders.history')->with('error', 'Đơn hàng này đã bị tự động hủy do quá hạn 15 phút chưa thanh toán.');
            }
            return redirect()->route('orders.history')->with('error', 'Đơn hàng này không thể thanh toán tiếp.');
        }

        // Tạo mã giao dịch trực tuyến MỚI riêng cho đơn hàng này để tránh bị gộp với đơn khác nếu trước đó mua nhiều đơn cùng lúc
        $newPayosOrderCode = intval(substr(time(), -7) . rand(10, 99));

        if ($order->thanhToan) {
            $order->thanhToan->payos_order_code = $newPayosOrderCode;
            $order->thanhToan->save();
        } else {
            // Fallback (chưa có thanh toán thì tạo mới)
            \App\Models\ThanhToan::create([
                'MaDonHang'        => $order->MaDonHang,
                'PhuongThuc'       => 'VietQR',
                'SoTien'           => $order->TongTien,
                'TrangThai'        => 'Chờ thanh toán',
                'NgayThanhToan'    => now(),
                'payos_order_code' => $newPayosOrderCode,
            ]);
        }

        $payOSService = app(\App\Services\PayOSService::class);
        $payosData = [
            'orderCode' => $newPayosOrderCode,
            'amount' => (int)$order->TongTien,
            'description' => 'Thanh toan TAFOOD',
            'cancelUrl' => route('checkout.payos-cancel', ['orderCode' => $newPayosOrderCode]),
            'returnUrl' => route('checkout.payos-return', ['orderCode' => $newPayosOrderCode]),
        ];

        try {
            $paymentLink = $payOSService->createPaymentLink($payosData);
            if ($paymentLink && isset($paymentLink['checkoutUrl'])) {
                return redirect()->away($paymentLink['checkoutUrl']);
            }
        } catch (\Exception $e) {
            logger()->error('Failed to create NEW PayOS payment link for order: ' . $maDonHang . ' Error: ' . $e->getMessage());
        }

        return redirect()->route('checkout.payment', $maDonHang)->with('error', 'Không thể khởi tạo lại thanh toán qua PayOS. Vui lòng thanh toán thủ công bằng mã QR bên dưới.');
    }


    /**
     * Get road distance between two points using OSRM, fallback to Haversine * 1.3
     */
    private function getDistance($lat1, $lng1, $lat2, $lng2)
    {
        $lat1 = (float)$lat1;
        $lng1 = (float)$lng1;
        $lat2 = (float)$lat2;
        $lng2 = (float)$lng2;

        try {
            $url = "http://router.project-osrm.org/route/v1/driving/{$lng1},{$lat1};{$lng2},{$lat2}?overview=false";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 3);
            curl_setopt($ch, CURLOPT_USERAGENT, 'TA-FOOD-App');
            $response = curl_exec($ch);
            curl_close($ch);

            if ($response) {
                $data = json_decode($response, true);
                if (isset($data['routes'][0]['distance'])) {
                    return round($data['routes'][0]['distance'] / 1000, 2);
                }
            }
        } catch (\Throwable $e) {
            logger()->warning('OSRM Distance API failed: ' . $e->getMessage());
        }

        // Fallback to Haversine formula (straight-line distance) and multiply by 1.3 to approximate road distance
        $earthRadius = 6371; // km
        $latDelta = deg2rad($lat1 - $lat2);
        $lonDelta = deg2rad($lng1 - $lng2);
        
        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos(deg2rad($lat2)) * cos(deg2rad($lat1)) *
             sin($lonDelta / 2) * sin($lonDelta / 2);
             
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $straightLineDistance = $earthRadius * $c;

        return round($straightLineDistance * 1.3, 2);
    }
}

