<?php

namespace App\Http\Controllers\Api;

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
            $query->where('TenMonAn', 'like', '%' . $request->search . '%');
        }
        $foods = $query->where('TrangThai', 'Còn bán')
            ->with(['nhaHang'])
            ->orderBy('created_at', 'desc')
            ->paginate($request->input('per_page', 12));
        return response()->json($foods);
    }

    // GET /api/v1/restaurants
    public function restaurants(Request $request)
    {
        $query = NhaHang::withCount(['monAn' => function ($q) {
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
