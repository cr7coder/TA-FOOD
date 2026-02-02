<?php

namespace App\Http\Controllers\Api;

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
        
        // Lấy danh sách món ăn của nhà hàng
        $foods = MonAn::where('MaNhaHang', $id)
            ->where('TrangThai', 'Còn bán')
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
        $query = MonAn::where('MaNhaHang', $id);

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
