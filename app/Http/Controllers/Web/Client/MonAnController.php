<?php

namespace App\Http\Controllers\Web\Client;

use App\Http\Controllers\Controller;

use App\Models\MonAn;
use App\Models\NhaHang;
use Illuminate\Http\Request;

class MonAnController extends Controller
{
    /**
     * Display a listing of the resource for the homepage.
     */
    public function menu(Request $request)
    {
        // Only load active and non-deleted categories
        $categories = \App\Models\DanhMuc::where('TrangThai', 'Hoạt động')->get(['TenDanhMuc']);
        $activeCategoryNames = $categories->pluck('TenDanhMuc')->toArray();

        // Only load foods that belong to active categories (Top 12 sorted by ratings & purchases)
        $foods = MonAn::whereIn('DanhMuc', $activeCategoryNames)
            ->with(['nhaHang'])
            ->withCount('donHangChiTiet')
            ->withAvg(['binhLuans' => function($q) {
                $q->where('trang_thai', 'Đã duyệt');
            }], 'diem_danh_gia')
            ->orderByRaw('COALESCE(binh_luans_avg_diem_danh_gia, 0) DESC')
            ->orderBy('don_hang_chi_tiet_count', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(12)
            ->get();

        $restaurants = NhaHang::hoatDong()->withCount(['monAn' => function ($q) use ($activeCategoryNames) {
            $q->where('TrangThai', 'Còn bán')->whereIn('DanhMuc', $activeCategoryNames);
        }])
            ->orderBy('TenNhaHang')
            ->get();

        // Fetch top 2 highest active and available promotions
        $promotions = \App\Models\GiamGia::active()
            ->available()
            ->orderByDesc('PhanTram')
            ->limit(2)
            ->get();

        // Fetch all active and available promotions for the Modal list
        $allPromotions = \App\Models\GiamGia::active()
            ->available()
            ->orderByDesc('PhanTram')
            ->get();

        // Fetch top recent positive reviews
        $reviews = \App\Models\BinhLuan::with(['nguoiDung', 'monAn'])
            ->where('trang_thai', 'Đã duyệt')
            ->where('diem_danh_gia', '>=', 4)
            ->whereNotNull('noi_dung')
            ->orderBy('created_at', 'desc')
            ->limit(6)
            ->get();

        return view('client.foods.index', compact('foods', 'restaurants', 'categories', 'promotions', 'allPromotions', 'reviews'));
    }

    /**
     * Display food detail.
     */
    public function detail($id)
    {
        $food = MonAn::with(['nhaHang', 'binhLuans.nguoiDung'])
            ->findOrFail($id);

        // Lấy món ăn liên quan (cùng danh mục)
        $relatedFoods = MonAn::where('DanhMuc', $food->DanhMuc)
            ->where('MaMonAn', '!=', $id)
            ->where('TrangThai', 'Còn bán')
            ->limit(4)
            ->get();

        return view('client.foods.detail', compact('food', 'relatedFoods'));
    }

    /**
     * Display the authenticated user's favorite foods.
     */
    public function favorites(Request $request)
    {
        $user = auth()->user();
        
        // Load user's favorite foods
        $foods = $user->yeuThichMonAns()
            ->with('nhaHang')
            ->paginate(12);

        // Fetch active categories to render tabs/filters if needed
        $categories = \App\Models\DanhMuc::where('TrangThai', 'Hoạt động')->get(['TenDanhMuc']);

        return view('client.foods.favorites', compact('foods', 'categories'));
    }

    /**
     * Toggle a food in/out of the user's favorites.
     */
    public function toggleFavorite(Request $request, $id)
    {
        $user = auth()->user();
        $food = MonAn::findOrFail($id);

        // Check if already favorited
        $isFavorite = $user->yeuThichMonAns()->where('mon_an.MaMonAn', $id)->exists();

        if ($isFavorite) {
            $user->yeuThichMonAns()->detach($id);
            $status = 'removed';
            $message = 'Đã xóa khỏi danh sách yêu thích!';
        } else {
            $user->yeuThichMonAns()->attach($id);
            $status = 'added';
            $message = 'Đã thêm vào danh sách yêu thích!';
        }

        return response()->json([
            'success' => true,
            'status' => $status,
            'message' => $message
        ]);
    }
}

