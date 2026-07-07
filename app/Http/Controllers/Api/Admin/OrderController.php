<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\DonHang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Lấy danh sách đơn hàng cho admin
     * GET /api/admin/orders
     */
    public function index(Request $request)
    {
        $query = DonHang::with(['chiTiet.monAn.nhaHang', 'nguoiDung', 'thanhToan']);

        // Filter by search (order code, customer name)
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('MaDonHang', 'LIKE', "%$search%")
                  ->orWhere('TenKhachHang', 'LIKE', "%$search%")
                  ->orWhereHas('chiTiet.monAn.nhaHang', function($sq) use ($search) {
                      $sq->where('TenNhaHang', 'LIKE', "%$search%");
                  });
            });
        }

        // Filter by status
        if ($request->has('status') && $request->input('status') !== 'all') {
            $query->where('TrangThai', $request->input('status'));
        }

        $perPage = $request->input('per_page', 10);
        $orders = $query->orderBy('created_at', 'desc')
            ->orderBy('MaDonHang', 'desc')
            ->paginate($perPage);

        $transformedOrders = $orders->getCollection()->map(function($order) {
            // Lấy tên cửa hàng (nếu có nhiều cửa hàng thì lấy cái đầu tiên + "...")
            $nhaHangs = $order->chiTiet->map(fn($ct) => $ct->monAn->nhaHang->TenNhaHang ?? 'N/A')->unique();
            $storeName = $nhaHangs->first();
            if ($nhaHangs->count() > 1) {
                $storeName .= ' (+' . ($nhaHangs->count() - 1) . ')';
            }

            return [
                'id' => $order->MaDonHang,
                'order_code' => 'ORD' . str_pad($order->MaDonHang, 4, '0', STR_PAD_LEFT),
                'customer_name' => $order->TenKhachHang,
                'store_name' => $storeName,
                'items_count' => $order->chiTiet->sum('SoLuong'),
                'total_amount' => (float)$order->TongTien,
                'status' => $order->TrangThai,
                'payment_method' => $order->PhuongThucThanhToan,
                'payment_status' => (function() use ($order) {
                    $status = $order->thanhToan->TrangThai ?? 'Chưa thanh toán';
                    // Nếu là COD mà chưa hoàn thành thì luôn là Chưa thanh toán (trừ khi đơn đã Hủy)
                    if ($order->PhuongThucThanhToan === 'COD' && $order->TrangThai !== 'Hoàn thành' && $order->TrangThai !== 'Hủy') {
                        return 'Chưa thanh toán';
                    }
                    return $status;
                })(),
                'created_at' => $order->created_at->format('Y-m-d H:i:s'),
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Lấy danh sách đơn hàng thành công',
            'data' => $transformedOrders,
            'pagination' => [
                'total' => $orders->total(),
                'per_page' => $orders->perPage(),
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
            ]
        ]);
    }

    /**
     * Lấy thống kê đơn hàng cho admin
     * GET /api/admin/orders/stats
     */
    public function stats()
    {
        $stats = DonHang::select('TrangThai', DB::raw('count(*) as total'))
            ->groupBy('TrangThai')
            ->get()
            ->pluck('total', 'TrangThai');

        return response()->json([
            'success' => true,
            'data' => [
                'cho_xac_nhan' => $stats->get('Chờ xử lý', 0),
                'da_xac_nhan' => $stats->get('Đã xác nhận', 0),
                'dang_chuan_bi' => $stats->get('Đang chuẩn bị', 0),
                'dang_giao' => $stats->get('Đang giao', 0),
                'hoan_thanh' => $stats->get('Hoàn thành', 0),
                'da_huy' => $stats->get('Hủy', 0),
                'total' => DonHang::count()
            ]
        ]);
    }

    /**
     * Lấy chi tiết đơn hàng
     * GET /api/admin/orders/{id}
     */
    public function show($id)
    {
        $order = DonHang::with(['chiTiet.monAn.nhaHang', 'nguoiDung', 'thanhToan', 'giamGia', 'doiTacVanChuyen'])
            ->findOrFail($id);

        $tamTinh = $order->chiTiet->sum(fn($i) => $i->Gia * $i->SoLuong);
        $giamGia = max(0, $tamTinh + (float)$order->PhiVanChuyen - (float)$order->TongTien);
        $tongTien = (float)$order->TongTien;

        // Lấy tên và địa chỉ cửa hàng
        $nhaHangs = $order->chiTiet->map(fn($ct) => $ct->monAn->nhaHang)->filter();
        $firstNhaHang = $nhaHangs->first();

        // Tính khoảng cách (Distance) ngược từ Phí ship khách trả
        $phiCoBan = $firstNhaHang ? (float)($firstNhaHang->phi_ship_co_ban ?? 15000) : 15000;
        $phiMoiKm = $firstNhaHang ? (float)($firstNhaHang->phi_ship_moi_km ?? 5000) : 5000;
        $kmMienPhi = $firstNhaHang ? (float)($firstNhaHang->km_mien_phi ?? 2.0) : 2.0;
        
        $phiShipKhachTra = (float)$order->PhiVanChuyen;
        $distance = 0.0;
        if ($phiShipKhachTra > 0) {
            if ($phiShipKhachTra <= $phiCoBan) {
                $distance = $kmMienPhi;
            } else {
                $extraFee = $phiShipKhachTra - $phiCoBan;
                $extraKm = $phiMoiKm > 0 ? ($extraFee / $phiMoiKm) : 0;
                $distance = round($kmMienPhi + $extraKm, 1);
            }
        } else {
            $distance = 1.2; // Tạm tính giao siêu gần miễn phí
        }

        // Tính chi phí thuê shipper thực tế theo biểu phí của hãng
        $carrierCost = 0;
        if ($order->doiTacVanChuyen) {
            $carrierBase = (float)$order->doiTacVanChuyen->phi_van_chuyen;
            $carrierKmPrice = (float)$order->doiTacVanChuyen->phi_km;
            $carrierCost = $carrierBase + ($distance * $carrierKmPrice);
        }

        // Mapping statuses
        $uiStatusMap = [
            'Chờ xử lý'     => 'Chờ xác nhận',
            'Đã xác nhận'   => 'Đã xác nhận',
            'Đang chuẩn bị' => 'Đang chuẩn bị',
            'Đang giao'     => 'Đang giao',
            'Hoàn thành'    => 'Hoàn thành',
            'Hủy'           => 'Đã hủy'
        ];
        $displayStatus = $uiStatusMap[$order->TrangThai] ?? $order->TrangThai;

        $relatedOrdersInfo = [];
        if ($order->thanhToan && !empty($order->thanhToan->MaGiaoDich)) {
            $groupCode = $order->thanhToan->MaGiaoDich;
            $siblings = DonHang::where('MaDonHang', '!=', $order->MaDonHang)
                ->whereHas('thanhToan', function($query) use ($groupCode) {
                    $query->where('MaGiaoDich', $groupCode);
                })
                ->with(['chiTiet.monAn.nhaHang'])
                ->get();
                
            foreach ($siblings as $sib) {
                $sibNhaHangs = $sib->chiTiet->map(fn($ct) => $ct->monAn->nhaHang->TenNhaHang ?? 'N/A')->unique();
                $sibStoreName = $sibNhaHangs->first();
                if ($sibNhaHangs->count() > 1) {
                    $sibStoreName .= ' (+' . ($sibNhaHangs->count() - 1) . ')';
                }
                $relatedOrdersInfo[] = [
                    'id' => $sib->MaDonHang,
                    'order_code' => 'ORD' . str_pad($sib->MaDonHang, 5, '0', STR_PAD_LEFT),
                    'store_name' => $sibStoreName,
                    'total_amount' => number_format((float)$sib->TongTien, 0, ',', '.') . ' đ',
                    'status' => $sib->TrangThai,
                ];
            }
        }

        $mappedData = [
            'MaDonHang' => $order->MaDonHang,
            'RelatedOrders' => $relatedOrdersInfo,
            'OrderCode' => 'ORD' . str_pad($order->MaDonHang, 5, '0', STR_PAD_LEFT),
            'TenKhachHang' => $order->TenKhachHang ?: ($order->nguoiDung->HoTen ?? 'Khách lẻ'),
            'SoDienThoai' => $order->SoDienThoai ?: ($order->nguoiDung->SoDienThoai ?? ''),
            'Email' => $order->nguoiDung->Email ?? 'Không có email',
            'DiaChi' => $order->DiaChiGiaoHang,
            'GhiChu' => $order->GhiChu,
            'PhuongThucThanhToan' => $order->PhuongThucThanhToan ?: 'Tiền mặt',
            'ThanhToan' => (function() use ($order) {
                $pt = $order->PhuongThucThanhToan ?: 'COD';
                $tt = $order->thanhToan->TrangThai ?? 'Chưa thanh toán';
                if ($pt === 'COD' && $order->TrangThai !== 'Hoàn thành') {
                    return 'Chưa thanh toán';
                }
                return $tt;
            })(),
            'TongTien' => number_format($tongTien, 0, ',', '.') . ' đ',
            'MinhChungThanhToan' => $order->thanhToan && $order->thanhToan->minh_chung_thanh_toan 
                ? asset('images/' . $order->thanhToan->minh_chung_thanh_toan) 
                : null,
            'TamTinh' => number_format($tamTinh, 0, ',', '.') . ' đ',
            'GiamGia' => '-' . number_format($giamGia, 0, ',', '.') . ' đ',
            'PhiShip' => number_format($order->PhiVanChuyen, 0, ',', '.') . ' đ',
            'PhiShipRaw' => (float)$order->PhiVanChuyen,
            'CarrierBaseFee' => $order->doiTacVanChuyen ? (float)$order->doiTacVanChuyen->phi_van_chuyen : 0,
            'CarrierBaseFeeFormat' => $order->doiTacVanChuyen ? number_format((float)$order->doiTacVanChuyen->phi_van_chuyen, 0, ',', '.') . ' đ' : '--',
            'CarrierKmFee' => $order->doiTacVanChuyen ? (float)$order->doiTacVanChuyen->phi_km : 0,
            'CarrierKmFeeFormat' => $order->doiTacVanChuyen ? number_format((float)$order->doiTacVanChuyen->phi_km, 0, ',', '.') . ' đ' : '--',
            'Distance' => $distance,
            'CarrierCost' => $carrierCost,
            'CarrierCostFormat' => number_format($carrierCost, 0, ',', '.') . ' đ',
            'FoodRevenue' => $tamTinh,
            'FoodRevenueFormat' => number_format($tamTinh, 0, ',', '.') . ' đ',
            'TotalMerchantOwed' => $tamTinh + ((float)$order->PhiVanChuyen - $carrierCost),
            'TotalMerchantOwedFormat' => number_format($tamTinh + ((float)$order->PhiVanChuyen - $carrierCost), 0, ',', '.') . ' đ',
            'ly_do_huy' => $order->ly_do_huy,
            'nguoi_huy' => $order->nguoi_huy,
            'TrangThai' => $displayStatus,
            'MaDoiTacVanChuyen' => $order->MaDoiTacVanChuyen,
            'TenDoiTacVanChuyen' => $order->doiTacVanChuyen->ten_doi_tac ?? 'Chưa chọn',
            'TenNhaHang' => $firstNhaHang->TenNhaHang ?? 'N/A',
            'DiaChiNhaHang' => $firstNhaHang->DiaChi ?? 'N/A',
            'ThoiGianRaw' => $order->created_at->format('d/m/Y - h:i A'),
            'TimelineTime' => [
                'Created' => $order->created_at->format('d/m/Y - h:i A'),
                'Confirmed' => $order->XacNhanAt ? $order->XacNhanAt->format('d/m/Y - h:i A') : '--',
                'Delivering' => $order->GiaoHangAt ? $order->GiaoHangAt->format('d/m/Y - h:i A') : '--',
                'Completed' => $order->HoanThanhAt ? $order->HoanThanhAt->format('d/m/Y - h:i A') : '--',
                'Cancelled' => $order->TrangThai === 'Hủy' ? $order->updated_at->format('d/m/Y - h:i A') : '--',
            ],
            'Items' => $order->chiTiet->map(fn($item) => [
                'TenMonAn' => $item->monAn->TenMonAn ?? 'Món đã xóa',
                'SoLuong' => $item->SoLuong,
                'Gia' => number_format($item->Gia, 0, ',', '.') . ' đ',
                'ThanhTien' => number_format($item->Gia * $item->SoLuong, 0, ',', '.') . ' đ',
                'HinhAnh' => $item->monAn->HinhAnh ?? null,
            ])
        ];

        $carriers = \App\Models\DoiTacVanChuyen::hoatDong()->get()->map(fn($c) => [
            'id' => $c->id,
            'ten_doi_tac' => $c->ten_doi_tac
        ]);

        return response()->json([
            'success' => true,
            'carriers' => $carriers,
            'data' => $mappedData
        ]);
    }

    /**
     * Cập nhật trạng thái đơn hàng
     * PUT /api/admin/orders/{id}/status
     */
    public function updateStatus(Request $request, $id)
    {
        $status = $request->input('status');
        
        $statusMap = [
            'Chờ xác nhận' => 'Chờ xử lý',
            'Đã xác nhận'  => 'Đã xác nhận',
            'Đang chuẩn bị' => 'Đang chuẩn bị',
            'Đang giao'    => 'Đang giao',
            'Hoàn thành'   => 'Hoàn thành',
            'Đã hủy'       => 'Hủy'
        ];
        $dbStatus = $statusMap[$status] ?? $status;
        $request->merge(['status' => $dbStatus]);

        $request->validate([
            'status' => 'required|string|in:Chờ xử lý,Đã xác nhận,Đang chuẩn bị,Đang giao,Hoàn thành,Hủy'
        ]);

        $order = DonHang::findOrFail($id);
        $oldStatus = $order->TrangThai;
        $newStatus = $request->input('status');

        if (in_array($oldStatus, ['Hoàn thành', 'Hủy'])) {
            return response()->json([
                'success' => false,
                'message' => 'Đơn hàng đã hoàn thành hoặc đã hủy thì không thể thay đổi trạng thái!'
            ], 422);
        }

        if ($newStatus === 'Đã xác nhận') {
            return response()->json([
                'success' => false,
                'message' => 'Admin không được phép xác nhận đơn hàng trực tiếp. Việc xác nhận đơn phải do Nhà hàng (Seller) thực hiện, hoặc hệ thống tự xác nhận khi Admin bấm "Duyệt thanh toán".'
            ], 422);
        }

        $order->TrangThai = $newStatus;
        
        if ($newStatus === 'Đã xác nhận' && $oldStatus !== 'Đã xác nhận') {
            $order->XacNhanAt = now();
        } elseif ($newStatus === 'Đang giao' && $oldStatus !== 'Đang giao') {
            $order->GiaoHangAt = now();
        } elseif ($newStatus === 'Hoàn thành' && $oldStatus !== 'Hoàn thành') {
            $order->HoanThanhAt = now();

            // Đồng bộ trạng thái thanh toán thành Đã thanh toán khi hoàn thành đơn
            if ($order->thanhToan) {
                $order->thanhToan->TrangThai = 'Đã thanh toán';
                $order->thanhToan->save();
            }
        } elseif ($newStatus === 'Hủy' && $oldStatus !== 'Hủy') {
            $order->ly_do_huy = $request->input('reason');
            $order->nguoi_huy = 'admin';

            // Đồng bộ trạng thái thanh toán thành Đã hoàn tiền nếu thanh toán online thành công, ngược lại là Thất bại
            if ($order->thanhToan) {
                // Hủy trực tiếp link trên cổng PayOS nếu chưa thanh toán
                if ($order->thanhToan->payos_order_code && in_array($order->thanhToan->TrangThai, ['Chờ thanh toán', 'Thất bại'])) {
                    try {
                        $payOSService = app(\App\Services\PayOSService::class);
                        $payOSService->cancelPaymentLink((int)$order->thanhToan->payos_order_code, $request->input('reason') ?? 'Quản trị viên hủy đơn');
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::error('Cancel PayOS link failed on admin cancel: ' . $e->getMessage());
                    }
                }

                if ($order->thanhToan->PhuongThuc !== 'COD' && $order->thanhToan->TrangThai === 'Đã thanh toán') {
                    $order->thanhToan->TrangThai = 'Đã hoàn tiền';
                } else {
                    $order->thanhToan->TrangThai = 'Thất bại';
                }
                $order->thanhToan->save();
            }
        }

        $order->save();

        // Gửi thông báo đến khách hàng
        if ($oldStatus !== $newStatus) {
            $statusMessages = [
                'Chờ xử lý'     => 'đang chờ xử lý.',
                'Đã xác nhận'   => 'đã được xác nhận.',
                'Đang chuẩn bị' => 'đang được nhà hàng chuẩn bị.',
                'Đang giao'     => 'đang được giao tới bạn.',
                'Hoàn thành'    => 'đã giao thành công! Chúc bạn ngon miệng! 🍕',
                'Hủy'           => 'đã bị hủy.'
            ];
            
            if ($newStatus === 'Hủy') {
                $isOnlineRefund = $order->thanhToan && $order->thanhToan->TrangThai === 'Đã hoàn tiền';
                $soTienFmt = $order->thanhToan ? number_format((float)$order->thanhToan->SoTien, 0, ',', '.') . ' đ' : '';

                if ($order->nguoi_huy === 'admin') {
                    if ($isOnlineRefund) {
                        $friendlyMsg = 'đã bị quản trị viên hủy. Lý do: ' . ($order->ly_do_huy ?: 'Không có lý do cụ thể.') . ' Hệ thống đã thực hiện hoàn tiền tự động số tiền ' . $soTienFmt . ' vào tài khoản của bạn. Vui lòng kiểm tra lại!';
                    } else {
                        $friendlyMsg = 'đã bị quản trị viên hủy. Lý do: ' . ($order->ly_do_huy ?: 'Không có lý do cụ thể.');
                    }
                } elseif ($order->nguoi_huy === 'seller') {
                    if ($isOnlineRefund) {
                        $friendlyMsg = 'đã bị nhà hàng hủy. Lý do: ' . ($order->ly_do_huy ?: 'Không có lý do cụ thể.') . ' Hệ thống đã thực hiện hoàn tiền tự động số tiền ' . $soTienFmt . ' vào tài khoản của bạn. Vui lòng kiểm tra lại!';
                    } else {
                        $friendlyMsg = 'đã bị nhà hàng hủy. Lý do: ' . ($order->ly_do_huy ?: 'Không có lý do cụ thể.');
                    }
                } else {
                    $friendlyMsg = 'đã bị hủy.';
                }
            } else {
                $friendlyMsg = $statusMessages[$newStatus] ?? 'đã được cập nhật.';
            }

            \App\Services\NotificationService::add(
                $order->MaNguoiDung,
                "Cập nhật đơn hàng",
                "Đơn hàng ORD" . str_pad($order->MaDonHang, 5, '0', STR_PAD_LEFT) . " của bạn " . $friendlyMsg,
                $order->MaDonHang
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật trạng thái đơn hàng thành công',
            'data' => $order
        ]);
    }

    /**
     * Admin xác nhận đã nhận tiền chuyển khoản
     * POST /api/v1/admin/orders/{id}/confirm-payment
     */
    public function confirmPayment(Request $request, $id)
    {
        $order = DonHang::with(['thanhToan', 'nguoiDung', 'chiTiet.monAn.nhaHang'])->findOrFail($id);

        if ($order->TrangThai !== 'Chờ xử lý') {
            return response()->json([
                'success' => false,
                'message' => 'Đơn hàng không ở trạng thái chờ xác nhận thanh toán!'
            ], 422);
        }

        try {
            DB::beginTransaction();

            $relatedOrders = collect([$order]);
            if ($order->thanhToan && !empty($order->thanhToan->MaGiaoDich)) {
                $groupCode = $order->thanhToan->MaGiaoDich;
                
                $siblingOrders = DonHang::where('TrangThai', 'Chờ xử lý')
                    ->where('MaDonHang', '!=', $order->MaDonHang)
                    ->whereHas('thanhToan', function($query) use ($groupCode) {
                        $query->where('MaGiaoDich', $groupCode);
                    })
                    ->with(['thanhToan', 'nguoiDung', 'chiTiet.monAn.nhaHang'])
                    ->get();
                    
                $relatedOrders = $relatedOrders->concat($siblingOrders);
            }

            foreach ($relatedOrders as $ord) {
                // 1. Cập nhật đơn hàng sang 'Đã xác nhận' (Seller sẽ xác nhận & chuẩn bị tiếp theo)
                $ord->TrangThai = 'Đã xác nhận';
                $ord->XacNhanAt = now();
                $ord->save();

                // 2. Cập nhật bảng thanh toán sang 'Đã thanh toán'
                if ($ord->thanhToan) {
                    $ord->thanhToan->TrangThai = 'Đã thanh toán';
                    $ord->thanhToan->NgayThanhToan = now();
                    $ord->thanhToan->save();
                }
            }

            DB::commit();

            $allCodes = [];
            foreach ($relatedOrders as $ord) {
                $currentCode = 'ORD' . str_pad($ord->MaDonHang, 5, '0', STR_PAD_LEFT);
                $allCodes[] = $currentCode;

                // 3. Thông báo cho Khách hàng
                \App\Services\NotificationService::add(
                    $ord->MaNguoiDung,
                    '✅ Thanh toán được xác nhận!',
                    "Admin đã xác nhận nhận tiền cho đơn hàng {$currentCode}. Nhà hàng sẽ sớm bắt đầu chuẩn bị món cho bạn! 🍳",
                    $ord->MaDonHang
                );

                // 4. Thông báo cho Seller
                $nhaHang = $ord->chiTiet->first()?->monAn?->nhaHang;
                if ($nhaHang) {
                    \App\Services\NotificationService::add(
                        $nhaHang->MaNguoiDung,
                        '🆕 Đơn hàng đã xác nhận — Cần chuẩn bị!',
                        "Đơn hàng {$currentCode} đã được Admin xác nhận thanh toán. Vui lòng bắt đầu chuẩn bị món!",
                        $ord->MaDonHang
                    );
                }
            }

            $message = "Đã xác nhận thanh toán cho " . implode(', ', $allCodes) . ". Đơn hàng chuyển sang trạng thái Đã xác nhận.";

            return response()->json([
                'success' => true,
                'message' => $message
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()], 500);
        }
    }

    public function updateCarrier(Request $request, $id)
    {
        $order = DonHang::findOrFail($id);

        if ($order->TrangThai === 'Chờ xử lý') {
            return response()->json([
                'success' => false,
                'message' => 'Đơn hàng chưa được xác nhận, không thể gán đối tác vận chuyển!'
            ], 422);
        }

        if (in_array($order->TrangThai, ['Hoàn thành', 'Hủy'])) {
            return response()->json([
                'success' => false,
                'message' => 'Đơn hàng đã hoàn thành hoặc đã hủy thì không thể thay đổi đối tác vận chuyển!'
            ], 422);
        }

        $carrierId = $request->input('carrier_id');
        
        if ($carrierId) {
            $carrier = \App\Models\DoiTacVanChuyen::findOrFail($carrierId);
            $order->MaDoiTacVanChuyen = $carrier->id;
        } else {
            $order->MaDoiTacVanChuyen = null;
        }

        $order->save();

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật đối tác vận chuyển thành công!',
            'carrier_name' => $order->doiTacVanChuyen->ten_doi_tac ?? 'Chưa chọn'
        ]);
    }
}
