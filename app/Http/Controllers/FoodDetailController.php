<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MonAn;
use App\Models\BinhLuan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class FoodDetailController extends Controller
{
    public function show($id)
    {
        $food = MonAn::with(['nhaHang', 'binhLuans.nguoiDung'])->findOrFail($id);

        // Lấy các món ăn liên quan (cùng danh mục)
        $relatedFoods = MonAn::where('DanhMuc', $food->DanhMuc)
            ->where('MaMonAn', '!=', $id)
            ->where('TrangThai', 'Còn bán')
            ->limit(4)
            ->get();

        return view('foods.detail', compact('food', 'relatedFoods'));
    }

    public function storeBinhLuan(Request $request, $id)
    {
        $request->validate([
            'diem_danh_gia' => 'required|integer|min:1|max:5',
            'noi_dung' => 'required|string|max:1000',
            'hinh_anh' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Bạn cần đăng nhập để bình luận.');
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

        return redirect()->back()->with('success', 'Đánh giá của bạn đã được thêm thành công!');
    }
}
