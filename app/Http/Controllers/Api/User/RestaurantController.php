<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\NhaHang;
use App\Models\MonAn;

class RestaurantController extends Controller
{
    // GET /api/v1/restaurants/{id}
    public function show($id)
    {
        $restaurant = NhaHang::with(['owner'])->findOrFail($id);
        $restaurant->append('hinh_anh_url');
        
        $activeCategoryNames = \App\Models\DanhMuc::where('TrangThai', 'Hoạt động')->pluck('TenDanhMuc')->toArray();

        // Lấy danh sách món ăn của nhà hàng
        $foods = MonAn::where('MaNhaHang', $id)
            ->where('TrangThai', 'Còn bán')
            ->whereIn('DanhMuc', $activeCategoryNames)
            ->orderBy('TenMonAn')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'restaurant' => $restaurant,
                'foods' => $foods
            ]
        ]);
    }

    // GET /api/v1/restaurants/{id}/foods
    public function foods($id, Request $request)
    {
        $activeCategoryNames = \App\Models\DanhMuc::where('TrangThai', 'Hoạt động')->pluck('TenDanhMuc')->toArray();
        $query = MonAn::where('MaNhaHang', $id)->whereIn('DanhMuc', $activeCategoryNames);

        if ($request->filled('category')) {
            $query->where('DanhMuc', $request->category);
        }

        if ($request->filled('search')) {
            $query->where('TenMonAn', 'like', '%' . $request->search . '%');
        }

        $foods = $query->where('TrangThai', 'Còn bán')
            ->orderBy('TenMonAn')
            ->paginate($request->input('per_page', 12));

        return response()->json($foods);
    }
}

