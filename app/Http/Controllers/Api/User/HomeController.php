<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\MonAn;
use App\Models\NhaHang;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    // GET /api/v1/foods
    public function foods(Request $request)
    {
        $query = MonAn::query();
        if ($request->filled('category')) {
            $query->where('DanhMuc', $request->category);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('TenMonAn', 'like', '%' . $search . '%')
                  ->orWhere('MoTa', 'like', '%' . $search . '%')
                  ->orWhere('DanhMuc', 'like', '%' . $search . '%');
            });
        }
        
        // Price filters
        if ($request->filled('min_price')) {
            $query->where('Gia', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('Gia', '<=', $request->max_price);
        }

        // Rating filter
        if ($request->filled('min_rating')) {
            $query->whereRaw('(SELECT COALESCE(AVG(diem_danh_gia), 0) FROM binh_luans WHERE binh_luans.MaMonAn = mon_an.MaMonAn AND binh_luans.trang_thai = "Đã duyệt") >= ?', [$request->min_rating]);
        }

        $query->where('TrangThai', 'Còn bán')
            ->with(['nhaHang'])
            ->whereHas('nhaHang', function ($q) {
                $q->hoatDong();
            })
            ->withCount('donHangChiTiet')
            ->withAvg(['binhLuans' => function($q) {
                $q->where('trang_thai', 'Đã duyệt');
            }], 'diem_danh_gia');

        // Sorting
        $sortBy = $request->input('sort_by', 'best_seller');
        switch ($sortBy) {
            case 'price_asc':
                $query->orderBy('Gia', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('Gia', 'desc');
                break;
            case 'top_rated':
                $query->orderByRaw('COALESCE(binh_luans_avg_diem_danh_gia, 0) DESC');
                break;
            case 'new':
                $query->orderBy('created_at', 'desc');
                break;
            case 'best_seller':
            default:
                $query->orderByRaw('COALESCE(binh_luans_avg_diem_danh_gia, 0) DESC')
                      ->orderBy('don_hang_chi_tiet_count', 'desc')
                      ->orderBy('created_at', 'desc');
                break;
        }

        $foods = $query->paginate($request->input('per_page', 12));
        return response()->json($foods);
    }

    // GET /api/v1/restaurants
    public function restaurants(Request $request)
    {
        $query = NhaHang::hoatDong()->withCount(['monAn' => function ($q) {
            $q->where('TrangThai', 'Còn bán');
        }]);
        if ($request->filled('search')) {
            $query->where('TenNhaHang', 'like', '%' . $request->search . '%');
        }
        $restaurants = $query->orderBy('TenNhaHang')->paginate($request->input('per_page', 12));
        return response()->json($restaurants);
    }

    // GET /api/v1/offers
    public function offers()
    {
        // Giả lập dữ liệu khuyến mãi
        $offers = [
            ["id" => 1, "title" => "Giảm 20% cho đơn đầu tiên", "image" => asset('images/o1.jpg'), "desc" => "Áp dụng cho khách mới."],
            ["id" => 2, "title" => "Miễn phí giao hàng cuối tuần", "image" => asset('images/o2.jpg'), "desc" => "Áp dụng cho đơn từ 200k."],
        ];
        return response()->json($offers);
    }

    // GET /api/v1/reviews
    public function reviews()
    {
        // Giả lập dữ liệu đánh giá khách hàng
        $reviews = [
            ["id" => 1, "name" => "Nguyễn Văn A", "avatar" => asset('images/client1.jpg'), "content" => "Đồ ăn ngon, giao nhanh!", "rating" => 5],
            ["id" => 2, "name" => "Trần Thị B", "avatar" => asset('images/client2.jpg'), "content" => "Nhà hàng sạch sẽ, phục vụ tốt.", "rating" => 4.5],
            ["id" => 3, "name" => "Lê Văn C", "avatar" => asset('images/client3.png'), "content" => "Giá hợp lý, nhiều khuyến mãi.", "rating" => 4],
        ];
        return response()->json($reviews);
    }
}

