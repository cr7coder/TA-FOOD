<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\DanhMuc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DanhMucController extends Controller
{
    /**
     * Check if user is admin
     */
    private function checkAdmin()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user || !$user->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Quyền truy cập bị từ chối. Chỉ quản trị viên mới có quyền này.'
            ], 403);
        }
        return null;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($response = $this->checkAdmin()) return $response;

        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');
        $status = $request->input('status');

        $query = DanhMuc::withCount('monAn')->orderBy('created_at', 'desc');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('TenDanhMuc', 'like', '%' . $search . '%')
                  ->orWhere('MoTa', 'like', '%' . $search . '%')
                  ->orWhere('Slug', 'like', '%' . $search . '%');
            });
        }

        if ($status) {
            $query->where('TrangThai', $status);
        }

        $categories = $query->paginate($perPage);

        // Calculate stats matching the user's mockup design
        $total = DanhMuc::count();
        $active = DanhMuc::where('TrangThai', 'Hoạt động')->count();
        $totalFoods = \App\Models\MonAn::count();
        $avgFoods = round($totalFoods / max(1, $total));

        return response()->json([
            'success' => true,
            'data' => [
                'items' => $categories->items(),
                'pagination' => [
                    'current_page' => $categories->currentPage(),
                    'last_page' => $categories->lastPage(),
                    'per_page' => $categories->perPage(),
                    'total' => $categories->total(),
                ],
                'stats' => [
                    'total' => $total,
                    'active' => $active,
                    'total_foods' => $totalFoods,
                    'avg_foods' => $avgFoods
                ]
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if ($response = $this->checkAdmin()) return $response;

        try {
            $validated = $request->validate([
                'TenDanhMuc' => 'required|string|max:100|unique:danh_mucs,TenDanhMuc',
                'Slug' => 'nullable|string|max:100|unique:danh_mucs,Slug',
                'MoTa' => 'nullable|string|max:255',
                'HinhAnh' => 'nullable|string|max:255',
                'HinhAnhFile' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'TrangThai' => 'nullable|string|in:Hoạt động,Khóa'
            ], [
                'TenDanhMuc.required' => 'Tên danh mục là bắt buộc.',
                'TenDanhMuc.unique' => 'Tên danh mục này đã tồn tại.',
                'TenDanhMuc.max' => 'Tên danh mục không được vượt quá 100 ký tự.',
                'Slug.unique' => 'Slug danh mục này đã tồn tại.',
                'Slug.max' => 'Slug danh mục không được vượt quá 100 ký tự.',
                'MoTa.max' => 'Mô tả không được vượt quá 255 ký tự.',
                'HinhAnh.max' => 'Đường dẫn ảnh không được vượt quá 255 ký tự.',
                'HinhAnhFile.image' => 'File tải lên phải là hình ảnh.',
                'HinhAnhFile.mimes' => 'Hình ảnh chỉ hỗ trợ định dạng jpeg, png, jpg, gif.',
                'HinhAnhFile.max' => 'Kích thước ảnh không vượt quá 2MB.',
                'TrangThai.in' => 'Trạng thái không hợp lệ (Hoạt động hoặc Khóa).'
            ]);

            // Mặc định trạng thái
            if (empty($validated['TrangThai'])) {
                $validated['TrangThai'] = 'Hoạt động';
            }

            // Handle file upload
            if ($request->hasFile('HinhAnhFile')) {
                $file = $request->file('HinhAnhFile');
                if ($file->isValid()) {
                    $dir = public_path('images/categories');
                    if (!file_exists($dir)) {
                        mkdir($dir, 0777, true);
                    }
                    $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $file->move($dir, $filename);
                    $validated['HinhAnh'] = '/images/categories/' . $filename;
                }
            }

            $category = DanhMuc::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Tạo danh mục thành công!',
                'data' => $category
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        if ($response = $this->checkAdmin()) return $response;

        $category = DanhMuc::find($id);

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy danh mục.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $category
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        if ($response = $this->checkAdmin()) return $response;

        $category = DanhMuc::find($id);

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy danh mục.'
            ], 404);
        }

        try {
            $validated = $request->validate([
                'TenDanhMuc' => 'required|string|max:100|unique:danh_mucs,TenDanhMuc,' . $id . ',MaDanhMuc',
                'Slug' => 'nullable|string|max:100|unique:danh_mucs,Slug,' . $id . ',MaDanhMuc',
                'MoTa' => 'nullable|string|max:255',
                'HinhAnh' => 'nullable|string|max:255',
                'HinhAnhFile' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'TrangThai' => 'required|string|in:Hoạt động,Khóa'
            ], [
                'TenDanhMuc.required' => 'Tên danh mục là bắt buộc.',
                'TenDanhMuc.unique' => 'Tên danh mục này đã tồn tại.',
                'TenDanhMuc.max' => 'Tên danh mục không được vượt quá 100 ký tự.',
                'Slug.unique' => 'Slug danh mục này đã tồn tại.',
                'Slug.max' => 'Slug danh mục không được vượt quá 100 ký tự.',
                'MoTa.max' => 'Mô tả không được vượt quá 255 ký tự.',
                'HinhAnh.max' => 'Đường dẫn ảnh không được vượt quá 255 ký tự.',
                'HinhAnhFile.image' => 'File tải lên phải là hình ảnh.',
                'HinhAnhFile.mimes' => 'Hình ảnh chỉ hỗ trợ định dạng jpeg, png, jpg, gif.',
                'HinhAnhFile.max' => 'Kích thước ảnh không vượt quá 2MB.',
                'TrangThai.required' => 'Trạng thái là bắt buộc.',
                'TrangThai.in' => 'Trạng thái không hợp lệ (Hoạt động hoặc Khóa).'
            ]);

            // Handle file upload
            if ($request->hasFile('HinhAnhFile')) {
                $file = $request->file('HinhAnhFile');
                if ($file->isValid()) {
                    $dir = public_path('images/categories');
                    if (!file_exists($dir)) {
                        mkdir($dir, 0777, true);
                    }
                    $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $file->move($dir, $filename);
                    
                    // Delete old file if exists
                    if (!empty($category->HinhAnh) && file_exists(public_path($category->HinhAnh))) {
                        @unlink(public_path($category->HinhAnh));
                    }
                    
                    $validated['HinhAnh'] = '/images/categories/' . $filename;
                }
            }

            $oldName = $category->TenDanhMuc;
            $category->update($validated);

            // Cascade name changes to all food items
            if ($oldName !== $category->TenDanhMuc) {
                \App\Models\MonAn::where('DanhMuc', $oldName)->update([
                    'DanhMuc' => $category->TenDanhMuc
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật danh mục thành công!',
                'data' => $category
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        if ($response = $this->checkAdmin()) return $response;

        $category = DanhMuc::find($id);

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy danh mục.'
            ], 404);
        }

        try {
            $category->delete();

            return response()->json([
                'success' => true,
                'message' => 'Xóa danh mục thành công!'
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
