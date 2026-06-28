<?php

namespace App\Http\Controllers\Web\Client;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Display the user's profile.
     */
    public function index()
    {
        $user = Auth::user();
        return view('client.profile.index', compact('user'));
    }

    /**
     * Show the form for editing the user's profile.
     */
    public function edit()
    {
        $user = Auth::user();
        return view('client.profile.edit', compact('user'));
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'HoTen' => ['required', 'string', 'max:255'],
            'Email' => ['required', 'string', 'email', 'max:255', 'unique:nguoi_dung,Email,' . $user->MaNguoiDung . ',MaNguoiDung'],
            'SoDienThoai' => ['nullable', 'string', 'max:20'],
            'DiaChi' => ['nullable', 'string', 'max:500'],
        ]);

        $user->update($validated);

        return redirect()->route('profile.index')->with('success', 'Cập nhật thông tin thành công!');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(6)],
        ]);

        $user = Auth::user();
        $user->update([
            'MatKhau' => Hash::make($validated['password']),
        ]);

        return redirect()->route('profile.index')->with('success', 'Đổi mật khẩu thành công!');
    }

    /**
     * Update only the user's default address via AJAX.
     */
    public function updateAddress(Request $request)
    {
        $validated = $request->validate([
            'DiaChi' => ['required', 'string', 'max:500'],
        ]);

        $user = Auth::user();
        $user->update([
            'DiaChi' => $validated['DiaChi']
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Đã lưu địa chỉ mặc định thành công!',
            'address' => $user->DiaChi
        ]);
    }

    /**
     * Update a tagged address (home/office/school) via AJAX.
     */
    public function updateTagAddress(Request $request)
    {
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

        $user = Auth::user();
        $user->update([
            $column => $validated['address']
        ]);

        return response()->json([
            'success' => true,
            'message' => "Đã lưu địa chỉ {$label} thành công!",
            'tag'     => $validated['tag'],
            'address' => $user->$column
        ]);
    }
}
