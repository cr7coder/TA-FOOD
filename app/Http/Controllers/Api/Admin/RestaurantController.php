<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\NhaHang;

class RestaurantController extends Controller
{
    public function index(Request $request)
    {
        $query = NhaHang::with('owner');

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where('TenNhaHang', 'like', "%$search%")
                  ->orWhere('SoDienThoai', 'like', "%$search%");
        }

        if ($request->has('status') && $request->status != '') {
            $query->where('TrangThai', $request->status);
        }

        $restaurants = $query->orderBy('created_at', 'desc')->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $restaurants->items(),
            'pagination' => [
                'current_page' => $restaurants->currentPage(),
                'last_page' => $restaurants->lastPage(),
                'per_page' => $restaurants->perPage(),
                'total' => $restaurants->total()
            ]
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Hoạt động,Chờ duyệt,Từ chối,Đóng cửa'
        ]);

        $restaurant = NhaHang::findOrFail($id);
        $oldStatus = $restaurant->TrangThai;
        $restaurant->TrangThai = $request->status;
        $restaurant->save();

        if ($oldStatus !== $request->status) {
            $message = '';
            if ($request->status === 'Hoạt động') {
                $message = 'Chúc mừng! Cửa hàng ' . $restaurant->TenNhaHang . ' của bạn đã được phê duyệt và đang hoạt động.';
            } else if ($request->status === 'Từ chối') {
                $message = 'Rất tiếc, cửa hàng ' . $restaurant->TenNhaHang . ' của bạn đã bị từ chối phê duyệt. Vui lòng liên hệ Admin.';
            } else if ($request->status === 'Đóng cửa') {
                $message = 'Cửa hàng ' . $restaurant->TenNhaHang . ' của bạn đã bị hệ thống tạm đóng.';
            }

            if ($message !== '') {
                \App\Services\NotificationService::add(
                    $restaurant->MaNguoiDung,
                    'Cập nhật trạng thái cửa hàng',
                    $message
                );
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật trạng thái nhà hàng thành công!',
            'data' => $restaurant
        ]);
    }
}
