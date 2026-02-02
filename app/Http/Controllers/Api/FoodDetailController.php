<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MonAn;
use App\Models\BinhLuan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class FoodDetailController extends Controller
{
    // GET /api/v1/foods/{id}
    public function show($id)
    {
        $food = MonAn::with(['nhaHang', 'binhLuans.nguoiDung'])->findOrFail($id);

        // Lấy các món ăn liên quan (cùng danh mục)
        $relatedFoods = MonAn::where('DanhMuc', $food->DanhMuc)
            ->where('MaMonAn', '!=', $id)
            ->where('TrangThai', 'Còn bán')
            ->limit(4)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'food' => $food,
                'relatedFoods' => $relatedFoods
            ]
        ]);
    }

    // POST /api/v1/foods/{id}/reviews
    public function storeReview(Request $request, $id)
    {
        $request->validate([
            'diem_danh_gia' => 'required|integer|min:1|max:5',
            'noi_dung' => 'required|string|max:1000',
            'hinh_anh' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn cần đăng nhập để bình luận.'
            ], 401);
        }

        $food = MonAn::findOrFail($id);

        $binhLuan = new BinhLuan();
        $binhLuan->ma_mon_an = $food->MaMonAn;
        $binhLuan->ma_nguoi_dung = Auth::user()->MaNguoiDung;
        $binhLuan->diem_danh_gia = $request->diem_danh_gia;
        $binhLuan->noi_dung = $request->noi_dung;

        // Xử lý upload ảnh
        if ($request->hasFile('hinh_anh')) {
            $file = $request->file('hinh_anh');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('images/reviews'), $fileName);
            $binhLuan->hinh_anh = 'reviews/' . $fileName;
        }

        $binhLuan->save();

        return response()->json([
            'success' => true,
            'message' => 'Đánh giá của bạn đã được thêm thành công!',
            'data' => $binhLuan
        ]);
    }
}
