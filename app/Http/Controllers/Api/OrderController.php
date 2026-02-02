<?php

namespace App\Http\Controllers\Api;

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
        
        $orders = DonHang::with(['chiTiet.monAn', 'giamGia', 'thanhToan'])
            ->where('MaNguoiDung', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Lấy danh sách đơn hàng thành công',
            'data' => $orders->map(function($order) {
                return [
                    'id' => $order->MaDonHang,
                    'order_code' => 'ORD' . str_pad($order->MaDonHang, 3, '0', STR_PAD_LEFT),
                    'total_amount' => $order->TongTien,
                    'status' => $order->TrangThai,
                    'delivery_address' => $order->DiaChiGiaoHang,
                    'customer_name' => $order->TenKhachHang,
                    'phone' => $order->SoDienThoai,
                    'payment_method' => $order->PhuongThucThanhToan,
                    'note' => $order->GhiChu,
                    'created_at' => $order->created_at->format('Y-m-d H:i:s'),
                    'items' => $order->chiTiet->map(function($item) {
                        return [
                            'food_id' => $item->MaMonAn,
                            'food_name' => $item->monAn->TenMonAn,
                            'quantity' => $item->SoLuong,
                            'price' => $item->Gia,
                            'subtotal' => $item->Gia * $item->SoLuong,
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
        $order = DonHang::with(['chiTiet.monAn', 'giamGia', 'thanhToan', 'nguoiDung'])
            ->where('MaDonHang', $id)
            ->where('MaNguoiDung', Auth::id())
            ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy đơn hàng',
            ], 404);
        }

        // Tính toán giảm giá
        $tamTinh = $order->chiTiet->sum(function($item) {
            return $item->Gia * $item->SoLuong;
        });
        
        $giamGia = 0;
        if ($order->MaGiamGia && $order->giamGia) {
            if ($order->giamGia->LoaiGiamGia == 'Phần trăm') {
                $giamGia = $tamTinh * ($order->giamGia->GiaTriGiam / 100);
                if ($order->giamGia->GiamToiDa) {
                    $giamGia = min($giamGia, $order->giamGia->GiamToiDa);
                }
            } else {
                $giamGia = $order->giamGia->GiaTriGiam;
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Lấy chi tiết đơn hàng thành công',
            'data' => [
                'id' => $order->MaDonHang,
                'order_code' => 'ORD' . str_pad($order->MaDonHang, 3, '0', STR_PAD_LEFT),
                'status' => $order->TrangThai,
                'total_amount' => $order->TongTien,
                'delivery_address' => $order->DiaChiGiaoHang,
                'customer_name' => $order->TenKhachHang,
                'phone' => $order->SoDienThoai,
                'payment_method' => $order->PhuongThucThanhToan,
                'note' => $order->GhiChu,
                'created_at' => $order->created_at->format('Y-m-d H:i:s'),
                'items' => $order->chiTiet->map(function($item) {
                    return [
                        'food_id' => $item->MaMonAn,
                        'food_name' => $item->monAn->TenMonAn,
                        'food_image' => $item->monAn->HinhAnh ? asset('images/' . $item->monAn->HinhAnh) : null,
                        'quantity' => $item->SoLuong,
                        'price' => $item->Gia,
                        'subtotal' => $item->Gia * $item->SoLuong,
                    ];
                }),
                'summary' => [
                    'subtotal' => $tamTinh,
                    'discount' => $giamGia,
                    'shipping_fee' => 0,
                    'total' => $order->TongTien,
                ],
                'voucher' => $order->giamGia ? [
                    'code' => $order->giamGia->MaGiamGia,
                    'type' => $order->giamGia->LoaiGiamGia,
                    'value' => $order->giamGia->GiaTriGiam,
                ] : null
            ]
        ], 200);
    }
}
