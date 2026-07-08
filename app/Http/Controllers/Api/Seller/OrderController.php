<?php

namespace App\Http\Controllers\Api\Seller;

use App\Http\Controllers\Controller;
use App\Models\DonHang;
use App\Models\NhaHang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $sellerId = Auth::user()->MaNguoiDung;
        $restaurant = NhaHang::where('MaNguoiDung', $sellerId)->first();
        if (!$restaurant) return response()->json(['success' => false, 'message' => 'Lỗi'], 404);

        $query = DonHang::with(['nguoiDung', 'chiTiet' => function($q) use ($restaurant) {
                $q->whereHas('monAn', function($q2) use ($restaurant) {
                    $q2->where('MaNhaHang', $restaurant->MaNhaHang);
                });
            }, 'chiTiet.monAn'])
            ->whereHas('chiTiet.monAn', function($q) use ($restaurant) {
                $q->where('MaNhaHang', $restaurant->MaNhaHang);
            });

        if ($request->has('status') && $request->status !== 'Tất cả') {
            $status = $request->status;
            // Map UI status back to DB status for filtering
            $statusMap = [
                'Chờ xác nhận' => 'Chờ xử lý',
                'Đã xác nhận'  => 'Đã xác nhận',
                'Đang chuẩn bị' => 'Đang chuẩn bị',
                'Đang giao'    => 'Đang giao',
                'Hoàn thành'   => 'Hoàn thành',
                'Đã hủy'       => 'Hủy'
            ];
            $dbStatus = $statusMap[$status] ?? $status;
            $query->where('TrangThai', $dbStatus);
        }

        $perPage = $request->input('per_page', 10);
        $orders = $query->orderBy('created_at', 'desc')
            ->orderBy('MaDonHang', 'desc')
            ->paginate($perPage);

        $formattedOrders = $orders->getCollection()->map(function ($order) {
            $resTotal = $order->chiTiet->sum(fn($ct) => $ct->SoLuong * $ct->Gia);
            
            // Unified UI Label Mapping
            $uiStatusMap = [
                'Chờ xử lý'     => 'Chờ xác nhận',
                'Đã xác nhận'   => 'Đã xác nhận',
                'Đang chuẩn bị' => 'Đang chuẩn bị',
                'Đang giao'     => 'Đang giao',
                'Hoàn thành'    => 'Hoàn thành',
                'Hủy'           => 'Đã hủy'
            ];
            $displayStatus = $uiStatusMap[$order->TrangThai] ?? $order->TrangThai;

            return [
                'MaDonHang' => $order->MaDonHang,
                'TenKhachHang' => $order->nguoiDung->HoTen ?? 'Khách lẻ',
                'MonAn' => implode(', ', $order->chiTiet->map(fn($d) => ($d->monAn->TenMonAn ?? 'Món đã xóa'))->toArray()),
                'TongTien' => number_format((float)$order->TongTien, 0, ',', '.') . ' đ',
                'FoodRevenue' => number_format($resTotal, 0, ',', '.') . ' đ',
                'TrangThai' => $displayStatus,
                'PhuongThucThanhToan' => $order->PhuongThucThanhToan,
                'ThanhToan' => (function() use ($order) {
                    $pt = $order->PhuongThucThanhToan ?: 'COD';
                    $tt = $order->thanhToan->TrangThai ?? 'Chưa thanh toán';
                    if ($pt === 'COD' && $order->TrangThai !== 'Hoàn thành' && $order->TrangThai !== 'Hủy') {
                        return 'Chưa thanh toán';
                    }
                    return $tt;
                })(),
                'ThoiGian' => date('H:i A', strtotime($order->created_at)),
                'NgayDat' => date('d/m/Y', strtotime($order->created_at)),
                'nguoi_huy' => $order->nguoi_huy,
                'ly_do_huy' => $order->ly_do_huy,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $formattedOrders,
            'pagination' => [
                'total' => $orders->total(),
                'per_page' => $orders->perPage(),
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
            ]
        ]);
    }

    public function show($id)
    {
        $sellerId = Auth::user()->MaNguoiDung;
        $restaurant = NhaHang::where('MaNguoiDung', $sellerId)->first();

        $order = DonHang::with(['nguoiDung', 'giamGia', 'thanhToan', 'chiTiet' => function($q) use ($restaurant) {
            $q->whereHas('monAn', function($q2) use ($restaurant) {
                $q2->where('MaNhaHang', $restaurant->MaNhaHang);
            });
        }, 'chiTiet.monAn'])->findOrFail($id);
        
        $tamTinh = $order->chiTiet->sum(fn($i) => $i->Gia * $i->SoLuong);
        $giamGia = max(0, $tamTinh + (float)$order->PhiVanChuyen - (float)$order->TongTien);
        $tongTien = (float)$order->TongTien;

        // Tính khoảng cách (Distance) ngược từ Phí ship khách trả
        $phiCoBan = $restaurant ? (float)($restaurant->phi_ship_co_ban ?? 15000) : 15000;
        $phiMoiKm = $restaurant ? (float)($restaurant->phi_ship_moi_km ?? 5000) : 5000;
        $kmMienPhi = $restaurant ? (float)($restaurant->km_mien_phi ?? 2.0) : 2.0;
        
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

        // Unified UI Label Mapping
        $uiStatusMap = [
            'Chờ xử lý'     => 'Chờ xác nhận',
            'Đã xác nhận'   => 'Đã xác nhận',
            'Đang chuẩn bị' => 'Đang chuẩn bị',
            'Đang giao'     => 'Đang giao',
            'Hoàn thành'    => 'Hoàn thành',
            'Hủy'           => 'Đã hủy'
        ];
        $displayStatus = $uiStatusMap[$order->TrangThai] ?? $order->TrangThai;

        $carriers = \App\Models\DoiTacVanChuyen::hoatDong()->get()->map(fn($c) => [
            'id' => $c->id,
            'ten_doi_tac' => $c->ten_doi_tac
        ]);

        return response()->json([
            'success' => true,
            'carriers' => $carriers,
            'data' => [
                'MaDonHang' => $order->MaDonHang,
                'OrderCode' => 'ORD' . str_pad($order->MaDonHang, 5, '0', STR_PAD_LEFT),
                'TenKhachHang' => $order->TenKhachHang ?: ($order->nguoiDung->HoTen ?? 'Khách lẻ'),
                'SoDienThoai' => $order->SoDienThoai ?: ($order->nguoiDung->SoDienThoai ?? ''),
                'DiaChi' => $order->DiaChiGiaoHang,
                'GhiChu' => $order->GhiChu,
                'ly_do_huy' => $order->ly_do_huy,
                'nguoi_huy' => $order->nguoi_huy,
                'PhuongThucThanhToan' => $order->PhuongThucThanhToan ?: 'Tiền mặt',
                'TongTien' => number_format($tongTien, 0, ',', '.') . ' đ',
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
                'CommissionRate' => $restaurant ? (float)($restaurant->commission_rate ?? 15.0) : 15.0,
                'CommissionAmount' => $restaurant ? round($tamTinh * ((float)($restaurant->commission_rate ?? 15.0) / 100), 0) : round($tamTinh * 0.15, 0),
                'CommissionAmountFormat' => number_format($restaurant ? round($tamTinh * ((float)($restaurant->commission_rate ?? 15.0) / 100), 0) : round($tamTinh * 0.15, 0), 0, ',', '.') . ' đ',
                'TotalMerchantOwed' => ($tamTinh - ($restaurant ? round($tamTinh * ((float)($restaurant->commission_rate ?? 15.0) / 100), 0) : round($tamTinh * 0.15, 0))) + ((float)$order->PhiVanChuyen - $carrierCost),
                'TotalMerchantOwedFormat' => number_format(($tamTinh - ($restaurant ? round($tamTinh * ((float)($restaurant->commission_rate ?? 15.0) / 100), 0) : round($tamTinh * 0.15, 0))) + ((float)$order->PhiVanChuyen - $carrierCost), 0, ',', '.') . ' đ',
                'TrangThai' => $displayStatus,
                'MaDoiTacVanChuyen' => $order->MaDoiTacVanChuyen,
                'TenDoiTacVanChuyen' => $order->doiTacVanChuyen->ten_doi_tac ?? 'Chưa chọn',
                'ThoiGianRaw' => $order->created_at->format('d/m/Y - h:i A'),
                // Thông tin thanh toán chi tiết
                'ThanhToanInfo' => [
                    'PhuongThuc'   => $order->thanhToan->PhuongThuc ?? $order->PhuongThucThanhToan ?? 'COD',
                    'TrangThai'    => $order->thanhToan->TrangThai ?? 'Chờ thanh toán',
                    'SoTien'       => $order->thanhToan ? number_format((float)$order->thanhToan->SoTien, 0, ',', '.') . ' đ' : null,
                    'NgayThanhToan'=> $order->thanhToan && $order->thanhToan->NgayThanhToan
                                        ? $order->thanhToan->NgayThanhToan->format('d/m/Y - H:i')
                                        : null,
                    'OrderCode'    => $order->thanhToan->payos_order_code ?? null,
                    'IsOnline'     => in_array($order->thanhToan->PhuongThuc ?? '', ['VietQR','Online','MoMo','ZaloPay','VNPay']),
                ],
                // Trả về thời gian thực tế của từng mốc
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
            ]
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $sellerId = Auth::user()->MaNguoiDung;
        $restaurant = NhaHang::where('MaNguoiDung', $sellerId)->first();
        
        $order = DonHang::whereHas('chiTiet.monAn', function($q) use ($restaurant) {
            $q->where('MaNhaHang', $restaurant->MaNhaHang);
        })->findOrFail($id);

        $status = $request->status;
        $now = Carbon::now();

        // Kiểm tra nếu đơn hàng đã bị hủy hoặc hoàn thành thì không cho phép cập nhật trạng thái nữa
        if ($order->TrangThai === 'Hủy') {
            return response()->json([
                'success' => false,
                'message' => 'Đơn hàng này đã bị hủy trước đó, không thể thay đổi trạng thái!'
            ], 422);
        }

        if ($order->TrangThai === 'Hoàn thành') {
            return response()->json([
                'success' => false,
                'message' => 'Đơn hàng này đã hoàn thành trước đó, không thể thay đổi trạng thái!'
            ], 422);
        }

        // Map UI Status to DB Status and handle timestamps
        if ($status === 'Đã xác nhận') {
            $order->TrangThai = 'Đã xác nhận';
            if (!$order->XacNhanAt) $order->XacNhanAt = $now;
        } elseif ($status === 'Đang chuẩn bị') {
            $order->TrangThai = 'Đang chuẩn bị';
            if (!$order->XacNhanAt) $order->XacNhanAt = $now;
        } elseif ($status === 'Đang giao') {
            if (!$order->MaDoiTacVanChuyen) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn phải chọn đối tác vận chuyển trước khi chuyển sang trạng thái Đang giao!'
                ], 422);
            }
            $order->TrangThai = 'Đang giao';
            if (!$order->XacNhanAt) $order->XacNhanAt = $now;
            if (!$order->GiaoHangAt) $order->GiaoHangAt = $now;
        } elseif ($status === 'Hoàn thành') {
            $order->TrangThai = 'Hoàn thành';
            if (!$order->XacNhanAt) $order->XacNhanAt = $now;
            if (!$order->GiaoHangAt) $order->GiaoHangAt = $now;
            if (!$order->HoanThanhAt) $order->HoanThanhAt = $now;

            // Đồng bộ trạng thái thanh toán thành Đã thanh toán khi hoàn thành đơn
            if ($order->thanhToan) {
                $order->thanhToan->TrangThai = 'Đã thanh toán';
                $order->thanhToan->save();
            }
        } elseif ($status === 'Đã hủy') {
            $order->TrangThai = 'Hủy';
            $order->ly_do_huy = $request->reason;
            $order->nguoi_huy = 'seller';

            // Đồng bộ trạng thái thanh toán và hủy link PayOS
            if ($order->thanhToan) {
                // Hủy trực tiếp link trên cổng PayOS nếu chưa thanh toán
                if ($order->thanhToan->payos_order_code && in_array($order->thanhToan->TrangThai, ['Chờ thanh toán', 'Thất bại'])) {
                    $payOSService = app(\App\Services\PayOSService::class);
                    $payOSService->cancelPaymentLink((int)$order->thanhToan->payos_order_code, $request->reason ?? 'Nhà hàng hủy đơn');
                }

                if ($order->thanhToan->PhuongThuc !== 'COD' && $order->thanhToan->TrangThai === 'Đã thanh toán') {
                    $order->thanhToan->TrangThai = 'Đã hoàn tiền';
                } else {
                    $order->thanhToan->TrangThai = 'Thất bại';
                }
                $order->thanhToan->save();
            }
        }

        $oldStatus = $order->getOriginal('TrangThai');
        $newStatus = $order->TrangThai;

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

        return response()->json(['success' => true, 'message' => 'Cập nhật thành công']);
    }

    public function updateCarrier(Request $request, $id)
    {
        $sellerId = Auth::user()->MaNguoiDung;
        $restaurant = NhaHang::where('MaNguoiDung', $sellerId)->first();
        
        $order = DonHang::whereHas('chiTiet.monAn', function($q) use ($restaurant) {
            $q->where('MaNhaHang', $restaurant->MaNhaHang);
        })->findOrFail($id);

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
