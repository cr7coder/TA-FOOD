<?php

namespace App\Http\Controllers\Api\Seller;

use App\Http\Controllers\Controller;
use App\Models\MonAn;
use App\Models\NhaHang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class FoodController extends Controller
{
    /**
     * Get list of foods for the authenticated seller
     */
    public function index(Request $request)
    {
        try {
            /** @var \App\Models\User $user */
            $user = Auth::user();

            if (!$user->isSeller()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access.'
                ], 403);
            }

            $search = $request->input('search');
            $perPage = $request->input('per_page', 10);

            $query = MonAn::ofSeller($user->MaNguoiDung)->with(['nhaHang']);

            if ($search) {
                $query->where('TenMonAn', 'like', '%' . $search . '%');
            }

            $foods = $query->orderBy('MaMonAn', 'desc')
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => [
                    'foods' => $foods->items(),
                    'pagination' => [
                        'current_page' => $foods->currentPage(),
                        'last_page' => $foods->lastPage(),
                        'per_page' => $foods->perPage(),
                        'total' => $foods->total(),
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lấy danh sách món ăn: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get detail of a specific food for the seller
     */
    public function show($id)
    {
        try {
            /** @var \App\Models\User $user */
            $user = Auth::user();
            $food = MonAn::with(['nhaHang'])->findOrFail($id);

            // Access control
            if (!$food->nhaHang || $food->nhaHang->MaNguoiDung !== $user->MaNguoiDung) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền xem món này.'
                ], 403);
            }

            return response()->json([
                'success' => true,
                'data' => $food
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Món ăn không tồn tại.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lấy chi tiết món ăn: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified food from storage via AJAX
     */
    public function destroy($id)
    {
        try {
            /** @var \App\Models\User $user */
            $user = Auth::user();
            $food = MonAn::findOrFail($id);

            // Access control
            if (!$food->nhaHang || $food->nhaHang->MaNguoiDung !== $user->MaNguoiDung) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền xóa món này.'
                ], 403);
            }

            $food->delete();

            return response()->json([
                'success' => true,
                'message' => 'Đã xóa món ăn thành công!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể xóa món ăn: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created food via AJAX
     */
    public function store(Request $request)
    {
        try {
            /** @var \App\Models\User $user */
            $user = Auth::user();
            $maNhaHang = NhaHang::where('MaNguoiDung', $user->MaNguoiDung)->value('MaNhaHang');
            
            if (!$maNhaHang) {
                return response()->json(['success' => false, 'message' => 'Tài khoản chưa được gán nhà hàng.'], 400);
            }

            if ($request->hasFile('HinhAnh') && !$request->has('images')) {
                $request->merge(['images' => [$request->file('HinhAnh')]]);
            }

            $validated = $request->validate([
                'TenMonAn'  => ['required', 'string', 'max:100', 'regex:/^[\p{L}\p{N}\s\-_]+$/u', 'unique:mon_an,TenMonAn'],
                'DanhMuc'   => ['required', 'string'],
                'MoTa'      => ['nullable', 'string'],
                'Gia'       => ['required', 'numeric', 'gt:0'],
                'TrangThai' => ['nullable', 'string'],
                'images'    => ['required', 'array', 'max:5'],
                'images.*'  => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            ], [
                'TenMonAn.required' => 'Tên món ăn không được bỏ trống (5E.1)',
                'TenMonAn.max' => 'Tên món ăn không quá 100 ký tự (5E.2)',
                'TenMonAn.regex' => 'Tên món ăn không chứa ký tự đặc biệt (5E.3)',
                'TenMonAn.unique' => 'Tên món ăn đã tồn tại (5E.4)',
                'Gia.required' => 'Giá không được bỏ trống (5E.5)',
                'Gia.gt' => 'Giá phải lớn hơn 0 (5E.6)',
                'Gia.numeric' => 'Giá phải là số (5E.7)',
                'images.required' => 'Hình ảnh không được bỏ trống (5E.8)',
                'images.*.image' => 'Định dạng hình ảnh không hợp lệ (5E.9)',
                'images.*.mimes' => 'Định dạng hình ảnh không hợp lệ (5E.9)',
                'images.*.max' => 'Kích thước hình ảnh quá lớn (5E.10)',
            ]);

            $filename = null;
            $thuVienAnh = [];
            if ($request->hasFile('images')) {
                $files = $request->file('images');
                foreach ($files as $index => $file) {
                    $uploadedFileUrl = \CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary::uploadApi()->upload(
                        $file->getRealPath(),
                        [
                            'folder' => 'ta-food/foods',
                            'resource_type' => 'auto'
                        ]
                    )['secure_url'];
                    
                    if ($index === 0) {
                        $filename = $uploadedFileUrl;
                    } else {
                        $thuVienAnh[] = $uploadedFileUrl;
                    }
                }
            }

            $food = MonAn::create([
                'MaNhaHang' => $maNhaHang,
                'TenMonAn'  => $validated['TenMonAn'],
                'DanhMuc'   => $validated['DanhMuc'],
                'MoTa'      => $validated['MoTa'] ?? null,
                'Gia'       => $validated['Gia'],
                'HinhAnh'   => $filename,
                'ThuVienAnh'=> json_encode($thuVienAnh),
                'TrangThai' => $validated['TrangThai'] ?? 'Còn bán',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Thêm món ăn thành công!',
                'data' => $food,
                'redirect' => route('seller.foods.index')
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Update the specified food via AJAX
     */
    public function update(Request $request, $id)
    {
        try {
            /** @var \App\Models\User $user */
            $user = Auth::user();
            
            $food = MonAn::find($id);
            if (!$food) {
                return response()->json([
                    'success' => false,
                    'message' => 'Món ăn không tồn tại (6E.1)'
                ], 404);
            }

            // Access control
            if (!$food->nhaHang || $food->nhaHang->MaNguoiDung !== $user->MaNguoiDung) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không có quyền sửa món ăn này (6E.2)'
                ], 403);
            }

            if ($request->hasFile('HinhAnh') && !$request->has('images')) {
                $request->merge(['images' => [$request->file('HinhAnh')]]);
            }

            $validated = $request->validate([
                'TenMonAn'  => ['required', 'string', 'max:100', 'regex:/^[\p{L}\p{N}\s\-_]+$/u', 'unique:mon_an,TenMonAn,'.$id.',MaMonAn'],
                'DanhMuc'   => ['required', 'string'],
                'MoTa'      => ['nullable', 'string'],
                'Gia'       => ['required', 'numeric', 'gt:0'],
                'TrangThai' => ['nullable', 'string'],
                'images'    => ['nullable', 'array', 'max:5'],
                'images.*'  => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            ], [
                'TenMonAn.required' => 'Tên món ăn không được bỏ trống (6E.3)',
                'TenMonAn.max' => 'Tên món ăn không quá 100 ký tự (6E.3)',
                'TenMonAn.regex' => 'Tên món ăn không chứa ký tự đặc biệt (6E.3)',
                'TenMonAn.unique' => 'Tên món ăn đã tồn tại (6E.4)',
                'Gia.required' => 'Giá phải lớn hơn 0 (6E.5)',
                'Gia.gt' => 'Giá phải lớn hơn 0 (6E.5)',
                'Gia.numeric' => 'Giá phải lớn hơn 0 (6E.5)',
            ]);

            // Check if any attributes actually changed
            $hasChanges = false;
            if ($food->TenMonAn !== $validated['TenMonAn']) $hasChanges = true;
            if ($food->DanhMuc !== $validated['DanhMuc']) $hasChanges = true;
            if ($food->Gia != $validated['Gia']) $hasChanges = true;
            if (($food->MoTa ?? '') !== ($validated['MoTa'] ?? '')) $hasChanges = true;
            if (($food->TrangThai ?? '') !== ($validated['TrangThai'] ?? '')) $hasChanges = true;
            if ($request->hasFile('images') || $request->hasFile('HinhAnh')) $hasChanges = true;

            if (!$hasChanges) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không có thông tin nào được cập nhật (6E.6)',
                    'errors' => [
                        'general' => ['Không có thông tin nào được cập nhật (6E.6)']
                    ]
                ], 422);
            }

            $updateData = [
                'TenMonAn'  => $validated['TenMonAn'],
                'DanhMuc'   => $validated['DanhMuc'],
                'MoTa'      => $validated['MoTa'] ?? null,
                'Gia'       => $validated['Gia'],
                'TrangThai' => $validated['TrangThai'] ?? 'Còn bán',
            ];

            if ($request->hasFile('images')) {
                // Delete old image
                if ($food->HinhAnh && !filter_var($food->HinhAnh, FILTER_VALIDATE_URL)) {
                    $oldPath = public_path('images/' . $food->HinhAnh);
                    if (File::exists($oldPath)) File::delete($oldPath);
                }
                if ($food->ThuVienAnh) {
                    $oldThuVien = json_decode($food->ThuVienAnh, true);
                    if (is_array($oldThuVien)) {
                        foreach ($oldThuVien as $oldName) {
                            if (!filter_var($oldName, FILTER_VALIDATE_URL)) {
                                $oldPath = public_path('images/' . $oldName);
                                  if (File::exists($oldPath)) File::delete($oldPath);
                            }
                        }
                    }
                }

                $filename = null;
                $thuVienAnh = [];
                $files = $request->file('images');
                foreach ($files as $index => $file) {
                    $uploadedFileUrl = \CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary::uploadApi()->upload(
                        $file->getRealPath(),
                        [
                            'folder' => 'ta-food/foods',
                            'resource_type' => 'auto'
                        ]
                    )['secure_url'];
                    
                    if ($index === 0) {
                        $filename = $uploadedFileUrl;
                    } else {
                        $thuVienAnh[] = $uploadedFileUrl;
                    }
                }
                $updateData['HinhAnh'] = $filename;
                $updateData['ThuVienAnh'] = json_encode($thuVienAnh);
            }

            $food->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Sửa món ăn thành công!',
                'redirect' => route('seller.foods.index')
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Helper for name unique check
     */
    public function checkName(Request $request)
    {
        $name = $request->input('name');
        $ignoreId = $request->input('ignore');
        
        $query = MonAn::where('TenMonAn', $name);
        if ($ignoreId) {
            $query->where('MaMonAn', '!=', $ignoreId);
        }
        
        if ($query->exists()) {
            return response()->json(['message' => 'Tên đã tồn tại'], 409);
        }
        return response()->json([], 204);
    }
}
