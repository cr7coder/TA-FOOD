<?php

namespace App\Http\Controllers\Api\User;

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

            // Only load active and non-deleted categories
            $activeCategoryNames = \App\Models\DanhMuc::where('TrangThai', 'Hoạt động')->pluck('TenDanhMuc')->toArray();

            // Build query
            $query = MonAn::with(['nhaHang'])
                ->whereHas('nhaHang', function ($q) {
                    $q->hoatDong();
                })
                ->where('TrangThai', 'Còn bán')
                ->whereIn('DanhMuc', $activeCategoryNames);

            // Filter by search (matches food name, description, or category name)
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('TenMonAn', 'like', '%' . $search . '%')
                      ->orWhere('MoTa', 'like', '%' . $search . '%')
                      ->orWhere('DanhMuc', 'like', '%' . $search . '%');
                });
            }

            // Filter by category
            if ($category) {
                $query->where('DanhMuc', $category);
            }

            // Filter by restaurant
            if ($restaurantId) {
                $query->where('MaNhaHang', $restaurantId);
            }

            // Paginate results (Order by highly rated and most purchased items first)
            $foods = $query->withCount('donHangChiTiet')
                ->withAvg(['binhLuans' => function($q) {
                    $q->where('trang_thai', 'Đã duyệt');
                }], 'diem_danh_gia')
                ->orderByRaw('COALESCE(binh_luans_avg_diem_danh_gia, 0) DESC')
                ->orderBy('don_hang_chi_tiet_count', 'desc')
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);

            // Get restaurants with food count
            $restaurants = NhaHang::hoatDong()->withCount(['monAn' => function ($q) use ($activeCategoryNames) {
                $q->where('TrangThai', 'Còn bán')->whereIn('DanhMuc', $activeCategoryNames);
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
            $activeCategoryNames = \App\Models\DanhMuc::where('TrangThai', 'Hoạt động')->pluck('TenDanhMuc')->toArray();

            $food = MonAn::with(['nhaHang', 'binhLuans.nguoiDung'])
                ->whereIn('DanhMuc', $activeCategoryNames)
                ->findOrFail($id);

            // Lấy món ăn liên quan (cùng danh mục)
            $relatedFoods = MonAn::where('DanhMuc', $food->DanhMuc)
                ->where('MaMonAn', '!=', $id)
                ->where('TrangThai', 'Còn bán')
                ->whereHas('nhaHang', function ($q) {
                    $q->hoatDong();
                })
                ->whereIn('DanhMuc', $activeCategoryNames)
                ->limit(4)
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Lấy thông tin món ăn thành công',
                'data' => [
                    'food' => $food,
                    'related_foods' => $relatedFoods
                ]
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
        try {
            $categories = \App\Models\DanhMuc::where('TrangThai', 'Hoạt động')
                ->pluck('TenDanhMuc')
                ->toArray();

            if (empty($categories)) {
                $categories = ['Cơm', 'Bún', 'Phở', 'Mì', 'Trà', 'Khác'];
            }

            return response()->json([
                'success' => true,
                'message' => 'Lấy danh mục thành công',
                'data' => $categories
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lấy danh mục thất bại',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

