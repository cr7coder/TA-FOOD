<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\DonHang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * Lấy danh sách đơn hàng của người dùng
     * GET /api/orders
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        
        $query = DonHang::with(['chiTiet.monAn', 'giamGia', 'thanhToan'])
            ->where('MaNguoiDung', Auth::id());

        // 1. Filter by Status
        if ($request->filled('status') && $request->input('status') !== 'all') {
            $status = $request->input('status');
            if ($status === 'pending') {
                $query->whereIn('TrangThai', ['Chờ xử lý', 'Đã xác nhận']);
            } elseif ($status === 'preparing') {
                $query->where('TrangThai', 'Đang chuẩn bị');
            } elseif ($status === 'shipping') {
                $query->where('TrangThai', 'Đang giao');
            } elseif ($status === 'completed') {
                $query->where('TrangThai', 'Hoàn thành');
            } elseif ($status === 'cancelled') {
                $query->where('TrangThai', 'Hủy');
            }
        }

        // 2. Filter by Search (Mã đơn hàng ORDxxx hoặc Tên món ăn)
        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function($q) use ($search) {
                if (preg_match('/ORD(\d+)/i', $search, $matches)) {
                    $q->where('MaDonHang', intval($matches[1]));
                } else {
                    $q->where('MaDonHang', 'like', "%{$search}%")
                      ->orWhereHas('chiTiet.monAn', function($sq) use ($search) {
                          $sq->where('TenMonAn', 'like', "%{$search}%");
                      });
                }
            });
        }

        // 3. Filter by Date range
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->input('start_date'));
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->input('end_date'));
        }

        $orders = $query->orderBy('created_at', 'desc')
            ->orderBy('MaDonHang', 'desc')
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Lấy danh sách đơn hàng thành công',
            'data' => $orders->map(function($order) {
                // Tính toán tạm tính (chưa gồm ship và giảm giá)
                $tamTinh = $order->chiTiet->sum(function($item) {
                    return floatval($item->Gia) * intval($item->SoLuong);
                });

                $phiShip = floatval($order->PhiVanChuyen ?? 0);
                $tongTienDb = floatval($order->TongTien);
                $giamGiaAmount = 0;

                $calculatedTotal = $tamTinh + $phiShip;

                if ($tongTienDb < $calculatedTotal) {
                    // Nếu tổng lưu trong DB nhỏ hơn tạm tính + ship, thì phần chênh lệch chính là giảm giá voucher!
                    $giamGiaAmount = $calculatedTotal - $tongTienDb;
                } else if ($tongTienDb > $calculatedTotal) {
                    // Nếu tổng lưu trong DB lớn hơn, ta tự động tăng phí ship tương ứng để khớp 100%
                    $phiShip = $tongTienDb - $tamTinh;
                    if ($phiShip < 0) {
                        $phiShip = 0;
                        $tongTienDb = $tamTinh;
                    }
                }

                return [
                    'id' => $order->MaDonHang,
                    'order_code' => 'ORD' . str_pad($order->MaDonHang, 3, '0', STR_PAD_LEFT),
                    'subtotal' => floatval($tamTinh),
                    'shipping_fee' => floatval($phiShip),
                    'discount_amount' => floatval($giamGiaAmount),
                    'voucher_code' => $order->giamGia ? $order->giamGia->MaGiamGia : null,
                    'total_amount' => floatval($tongTienDb),
                    'status' => (function($status) {
                        $map = [
                            'Chờ xử lý'     => 'Chờ xác nhận',
                            'Đã xác nhận'   => 'Đã xác nhận',
                            'Đang chuẩn bị' => 'Đang chuẩn bị',
                            'Đang giao'     => 'Đang giao',
                            'Hoàn thành'    => 'Hoàn thành',
                            'Hủy'           => 'Đã hủy'
                        ];
                        return $map[$status] ?? $status;
                    })($order->TrangThai),
                    'delivery_address' => $order->DiaChiGiaoHang,
                    'customer_name' => $order->TenKhachHang,
                    'phone' => $order->SoDienThoai,
                    'payment_method' => $order->PhuongThucThanhToan,
                    'note' => $order->GhiChu,
                    'created_at' => $order->created_at->format('Y-m-d H:i:s'),
                    'items' => $order->chiTiet->map(function($item) use ($order) {
                        return [
                            'food_id' => $item->MaMonAn,
                            'food_name' => $item->monAn->TenMonAn,
                            'food_image' => $item->monAn ? $item->monAn->hinh_anh_url : null,
                            'quantity' => $item->SoLuong,
                            'price' => floatval($item->Gia),
                            'subtotal' => floatval($item->Gia * $item->SoLuong),
                            'has_reviewed' => \App\Models\BinhLuan::hasReviewed($order->MaNguoiDung, $item->MaMonAn, $order->MaDonHang),
                        ];
                    })
                ];
            }),
            'pagination' => [
                'total' => $orders->total(),
                'per_page' => $orders->perPage(),
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
            ]
        ], 200);
    }

    /**
     * Lấy chi tiết đơn hàng
     * GET /api/orders/{id}
     */
    public function show($id)
    {
        $order = DonHang::with(['chiTiet.monAn', 'giamGia', 'thanhToan', 'nguoiDung', 'doiTacVanChuyen'])
            ->where('MaDonHang', $id)
            ->where('MaNguoiDung', Auth::id())
            ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy đơn hàng',
            ], 404);
        }

        // Tính toán giảm giá và đồng bộ toán học
        $tamTinh = $order->chiTiet->sum(function($item) {
            return floatval($item->Gia) * intval($item->SoLuong);
        });
        
        $phiShip = floatval($order->PhiVanChuyen ?? 0);
        $tongTienDb = floatval($order->TongTien);
        $giamGiaAmount = 0;

        $calculatedTotal = $tamTinh + $phiShip;

        if ($tongTienDb < $calculatedTotal) {
            // Phần chênh lệch chính là giảm giá voucher!
            $giamGiaAmount = $calculatedTotal - $tongTienDb;
        } else if ($tongTienDb > $calculatedTotal) {
            // Tự động tăng phí ship để khớp 100%
            $phiShip = $tongTienDb - $tamTinh;
            if ($phiShip < 0) {
                $phiShip = 0;
                $tongTienDb = $tamTinh;
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Lấy chi tiết đơn hàng thành công',
            'data' => [
                'id' => $order->MaDonHang,
                'order_code' => 'ORD' . str_pad($order->MaDonHang, 5, '0', STR_PAD_LEFT),
                'status' => (function($status) {
                    $map = [
                        'Chờ xử lý'     => 'Chờ xác nhận',
                        'Đã xác nhận'   => 'Đã xác nhận',
                        'Đang chuẩn bị' => 'Đang chuẩn bị',
                        'Đang giao'     => 'Đang giao',
                        'Hoàn thành'    => 'Hoàn thành',
                        'Hủy'           => 'Đã hủy'
                    ];
                    return $map[$status] ?? $status;
                })($order->TrangThai),
                'total_amount' => floatval($tongTienDb),
                'delivery_address' => $order->DiaChiGiaoHang,
                'customer_name' => $order->TenKhachHang,
                'phone' => $order->SoDienThoai,
                'payment_method' => $order->PhuongThucThanhToan,
                'payment_status' => (function() use ($order) {
                    $pt = $order->PhuongThucThanhToan ?: 'COD';
                    $tt = $order->thanhToan->TrangThai ?? 'Chưa thanh toán';
                    if ($pt === 'COD' && $order->TrangThai !== 'Hoàn thành' && $order->TrangThai !== 'Hủy') {
                        return 'Chưa thanh toán';
                    }
                    return $tt;
                })(),
                'carrier_name' => $order->doiTacVanChuyen->ten_doi_tac ?? 'Chưa có',
                'cancel_reason' => $order->ly_do_huy,
                'cancelled_by' => $order->nguoi_huy,
                'timeline' => [
                    'created' => $order->created_at->format('Y-m-d H:i:s'),
                    'confirmed' => $order->XacNhanAt ? $order->XacNhanAt->format('Y-m-d H:i:s') : null,
                    'delivering' => $order->GiaoHangAt ? $order->GiaoHangAt->format('Y-m-d H:i:s') : null,
                    'completed' => $order->HoanThanhAt ? $order->HoanThanhAt->format('Y-m-d H:i:s') : null,
                    'cancelled' => $order->TrangThai === 'Hủy' ? $order->updated_at->format('Y-m-d H:i:s') : null,
                ],
                'note' => $order->GhiChu,
                'created_at' => $order->created_at->format('Y-m-d H:i:s'),
                'items' => $order->chiTiet->map(function($item) use ($order) {
                    return [
                        'food_id' => $item->MaMonAn,
                        'food_name' => $item->monAn->TenMonAn,
                        'food_image' => $item->monAn ? $item->monAn->hinh_anh_url : null,
                        'quantity' => $item->SoLuong,
                        'price' => floatval($item->Gia),
                        'subtotal' => floatval($item->Gia * $item->SoLuong),
                        'has_reviewed' => \App\Models\BinhLuan::hasReviewed($order->MaNguoiDung, $item->MaMonAn, $order->MaDonHang),
                    ];
                }),
                'summary' => [
                    'subtotal' => floatval($tamTinh),
                    'discount' => floatval($giamGiaAmount),
                    'shipping_fee' => floatval($phiShip),
                    'total' => floatval($tongTienDb),
                ],
                'voucher' => $order->giamGia ? [
                    'code' => $order->giamGia->MaGiamGia,
                    'type' => $order->giamGia->LoaiGiamGia,
                    'value' => $order->giamGia->GiaTriGiam,
                ] : null
            ]
        ], 200);
    }

    /**
     * Hủy đơn hàng
     * POST /api/v1/orders/{id}/cancel
     */
    public function cancel(Request $request, $id)
    {
        $order = DonHang::where('MaDonHang', $id)
            ->where('MaNguoiDung', \Illuminate\Support\Facades\Auth::id())
            ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy đơn hàng của bạn',
            ], 404);
        }

        // Chỉ cho phép hủy khi ở trạng thái "Chờ xử lý" hoặc "Đã thanh toán"
        $allowedStatuses = ['Chờ xử lý', 'Đã xác nhận'];
        if (!in_array($order->TrangThai, $allowedStatuses)) {
            return response()->json([
                'success' => false,
                'message' => 'Đơn hàng đã được nhà hàng tiếp nhận (' . $order->TrangThai . '), không thể hủy trực tuyến!',
            ], 400);
        }

        // Cập nhật trạng thái thành "Hủy"
        $order->TrangThai = 'Hủy';
        $order->nguoi_huy = 'customer';
        $order->ly_do_huy = $request->input('reason', 'Khách hàng chủ động hủy đơn');
        $order->save();

        if ($order->MaGiamGia && $order->giamGia) {
            $order->giamGia->decrementUsage();
        }

        // Gửi thông báo đến khách hàng
        \App\Services\NotificationService::add(
            $order->MaNguoiDung,
            "Hủy đơn hàng thành công",
            "Đơn hàng ORD" . str_pad($order->MaDonHang, 5, '0', STR_PAD_LEFT) . " của bạn đã được bạn hủy thành công.",
            $order->MaDonHang
        );

        return response()->json([
            'success' => true,
            'message' => 'Hủy đơn hàng thành công',
            'data' => [
                'id' => $order->MaDonHang,
                'status' => 'Đã hủy'
            ]
        ], 200);
    }
}

