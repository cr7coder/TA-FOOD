<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MonAn;
use App\Models\NhaHang;
use Illuminate\Http\Request;

class FoodController extends Controller
{
    /**
     * Get list of foods for homepage
     * GET /api/v1/foods
     */
    public function index(Request $request)
    {
        try {
            // Lấy parameters từ request
            $perPage = $request->input('per_page', 12);
            $page = $request->input('page', 1);
            $search = $request->input('search');
            $category = $request->input('category');
            $restaurantId = $request->input('restaurant_id');

            // Build query
            $query = MonAn::with(['nhaHang'])
                ->where('TrangThai', 'Còn bán');

            // Filter by search
            if ($search) {
                $query->where('TenMonAn', 'like', '%' . $search . '%');
            }

            // Filter by category
            if ($category) {
                $query->where('DanhMuc', $category);
            }

            // Filter by restaurant
            if ($restaurantId) {
                $query->where('MaNhaHang', $restaurantId);
            }

            // Paginate results
            $foods = $query->orderBy('created_at', 'desc')
                ->paginate($perPage);

            // Get restaurants with food count
            $restaurants = NhaHang::withCount(['monAn' => function ($q) {
                $q->where('TrangThai', 'Còn bán');
            }])
                ->orderBy('TenNhaHang')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Lấy danh sách món ăn thành công',
                'data' => [
                    'foods' => [
                        'data' => $foods->items(),
                        'current_page' => $foods->currentPage(),
                        'last_page' => $foods->lastPage(),
                        'per_page' => $foods->perPage(),
                        'total' => $foods->total(),
                        'from' => $foods->firstItem(),
                        'to' => $foods->lastItem(),
                    ],
                    'restaurants' => $restaurants,
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi lấy danh sách món ăn',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get food detail
     * GET /api/v1/foods/{id}
     */
    public function show($id)
    {
        try {
            $food = MonAn::with(['nhaHang', 'binhLuans.nguoiDung'])
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Lấy thông tin món ăn thành công',
                'data' => $food
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy món ăn'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi lấy thông tin món ăn',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get food categories
     * GET /api/v1/foods/categories
     */
    public function categories()
    {
        $categories = ['Cơm', 'Bún', 'Phở', 'Mì', 'Trà', 'Khác'];

        return response()->json([
            'success' => true,
            'message' => 'Lấy danh mục thành công',
            'data' => $categories
        ], 200);
    }
}
