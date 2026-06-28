<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller
{
    /**
     * Get user profile information
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request)
    {
        try {
            $user = $request->user();

            return response()->json([
                'success' => true,
                'data' => [
                    'MaNguoiDung'      => $user->MaNguoiDung,
                    'TenDangNhap'      => $user->TenDangNhap,
                    'HoTen'            => $user->HoTen,
                    'Email'            => $user->Email,
                    'SoDienThoai'      => $user->SoDienThoai,
                    'DiaChi'           => $user->DiaChi,
                    'DiaChiNhaRieng'   => $user->DiaChiNhaRieng,
                    'DiaChiVanPhong'   => $user->DiaChiVanPhong,
                    'DiaChiTruongHoc'  => $user->DiaChiTruongHoc,
                    'VaiTro'           => $user->VaiTro,
                    'AnhDaiDien'       => $user->AnhDaiDien,
                    'avatar_url'       => $user->avatar_url,
                    'google_id'        => $user->google_id,
                    'NgayTao'          => $user->created_at ? $user->created_at->format('Y-m-d H:i:s') : null,
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể lấy thông tin người dùng',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Update user profile information
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        try {
            $user = $request->user();

            $validated = $request->validate([
                'HoTen'           => ['required', 'string', 'max:255'],
                'Email'           => ['required', 'string', 'email', 'max:255', 'unique:nguoi_dung,Email,' . $user->MaNguoiDung . ',MaNguoiDung'],
                'SoDienThoai'     => ['nullable', 'string', 'max:20'],
                'DiaChi'          => ['nullable', 'string', 'max:500'],
                'DiaChiNhaRieng'  => ['nullable', 'string', 'max:500'],
                'DiaChiVanPhong'  => ['nullable', 'string', 'max:500'],
                'DiaChiTruongHoc' => ['nullable', 'string', 'max:500'],
                'AnhDaiDien'      => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            ], [
                'HoTen.required'  => 'Họ và tên là bắt buộc',
                'HoTen.max'       => 'Họ và tên không được vượt quá 255 ký tự',
                'Email.required'  => 'Email là bắt buộc',
                'Email.email'     => 'Email không hợp lệ',
                'Email.unique'    => 'Email đã được sử dụng',
                'SoDienThoai.max' => 'Số điện thoại không được vượt quá 20 ký tự',
                'DiaChi.max'      => 'Địa chỉ không được vượt quá 500 ký tự',
                'AnhDaiDien.image' => 'File tải lên phải là hình ảnh',
                'AnhDaiDien.mimes' => 'Hình ảnh phải có định dạng jpeg, png, jpg, hoặc gif',
                'AnhDaiDien.max'  => 'Kích thước hình ảnh không được vượt quá 2MB',
            ]);

            // Handing Avatar upload
            if ($request->hasFile('AnhDaiDien')) {
                // Delete old local avatar
                if ($user->AnhDaiDien && !filter_var($user->AnhDaiDien, FILTER_VALIDATE_URL)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($user->AnhDaiDien);
                }
                $uploadedFileUrl = \CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary::uploadApi()->upload(
                    $request->file('AnhDaiDien')->getRealPath(),
                    [
                        'folder' => 'ta-food/avatars',
                        'resource_type' => 'auto'
                    ]
                )['secure_url'];
                $user->AnhDaiDien = $uploadedFileUrl;
            }

            // Remove AnhDaiDien from standard Eloquent update attributes to avoid overwriting path
            unset($validated['AnhDaiDien']);
            $user->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật thông tin thành công',
                'data' => [
                    'MaNguoiDung'      => $user->MaNguoiDung,
                    'TenDangNhap'      => $user->TenDangNhap,
                    'HoTen'            => $user->HoTen,
                    'Email'            => $user->Email,
                    'SoDienThoai'      => $user->SoDienThoai,
                    'DiaChi'           => $user->DiaChi,
                    'DiaChiNhaRieng'   => $user->DiaChiNhaRieng,
                    'DiaChiVanPhong'   => $user->DiaChiVanPhong,
                    'DiaChiTruongHoc'  => $user->DiaChiTruongHoc,
                    'VaiTro'           => $user->VaiTro,
                    'AnhDaiDien'       => $user->AnhDaiDien,
                    'avatar_url'       => $user->avatar_url,
                    'google_id'        => $user->google_id,
                ]
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể cập nhật thông tin',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Update a single tagged address (nha_rieng/van_phong/truong_hoc)
     */
    public function updateTagAddress(Request $request)
    {
        try {
            $user = $request->user();

            $validated = $request->validate([
                'tag'     => ['required', 'in:nha_rieng,van_phong,truong_hoc'],
                'address' => ['required', 'string', 'max:500'],
            ]);

            $columnMap = [
                'nha_rieng'  => 'DiaChiNhaRieng',
                'van_phong'  => 'DiaChiVanPhong',
                'truong_hoc' => 'DiaChiTruongHoc',
            ];

            $labelMap = [
                'nha_rieng'  => 'Nhà riêng',
                'van_phong'  => 'Văn phòng',
                'truong_hoc' => 'Trường học',
            ];

            $column = $columnMap[$validated['tag']];
            $label  = $labelMap[$validated['tag']];

            $user->update([$column => $validated['address']]);

            return response()->json([
                'success' => true,
                'message' => "Đã lưu địa chỉ {$label} thành công!",
                'tag'     => $validated['tag'],
                'address' => $user->$column
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể lưu địa chỉ',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Update user password
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updatePassword(Request $request)
    {
        try {
            $user = $request->user();

            $validated = $request->validate([
                'current_password' => ['required', 'string'],
                'password' => ['required', 'confirmed', Password::min(6)],
            ], [
                'current_password.required' => 'Mật khẩu hiện tại là bắt buộc',
                'password.required' => 'Mật khẩu mới là bắt buộc',
                'password.confirmed' => 'Xác nhận mật khẩu không khớp',
                'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự',
            ]);

            // Verify current password
            if (!Hash::check($validated['current_password'], $user->MatKhau)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mật khẩu hiện tại không đúng',
                    'errors' => [
                        'current_password' => ['Mật khẩu hiện tại không đúng']
                    ]
                ], 422);
            }

            // Update password
            $user->update([
                'MatKhau' => Hash::make($validated['password']),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Đổi mật khẩu thành công'
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể đổi mật khẩu',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Delete user account
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request)
    {
        try {
            $user = $request->user();

            $validated = $request->validate([
                'password' => ['required', 'string'],
            ], [
                'password.required' => 'Mật khẩu là bắt buộc để xác nhận xóa tài khoản',
            ]);

            // Verify password
            if (!Hash::check($validated['password'], $user->MatKhau)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mật khẩu không đúng',
                    'errors' => [
                        'password' => ['Mật khẩu không đúng']
                    ]
                ], 422);
            }

            // Delete all user tokens
            $user->tokens()->delete();

            // Delete user account
            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'Xóa tài khoản thành công'
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể xóa tài khoản',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
}

