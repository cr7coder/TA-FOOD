<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Hiển thị form đăng nhập
     */
    public function showLoginForm()
    {
        // Nếu đã đăng nhập, redirect về trang chủ
        if (Auth::check()) {
            return redirect()->route('foods.index');
        }

        return view('auth.login');
    }

    /**
     * Xử lý đăng nhập
     */
    public function login(Request $request)
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'login' => 'required|string',
            'password' => 'required|string',
        ], [
            'login.required' => 'Vui lòng nhập email hoặc tên đăng nhập',
            'password.required' => 'Vui lòng nhập mật khẩu',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $login = $request->input('login');
        $password = $request->input('password');
        $remember = $request->boolean('remember');

        // Xác định login field (email hoặc username)
        $loginField = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'Email' : 'TenDangNhap';

        // Tìm user
        $user = User::where($loginField, $login)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Tài khoản không tồn tại trong hệ thống'
            ], 401);
        }

        // Kiểm tra mật khẩu
        if (!password_verify($password, $user->MatKhau)) {
            return response()->json([
                'success' => false,
                'message' => 'Mật khẩu không chính xác'
            ], 401);
        }

        // Đăng nhập thành công
        Auth::login($user, $remember);

        // Trigger MergeSessionCart listener (nếu có)
        event(new \Illuminate\Auth\Events\Login('web', $user, $remember));

        return response()->json([
            'success' => true,
            'message' => 'Đăng nhập thành công',
            'user' => [
                'id' => $user->MaNguoiDung,
                'name' => $user->HoTen,
                'email' => $user->Email,
                'role' => $user->VaiTro,
            ],
            'redirect' => session('url.intended', route('foods.index'))
        ]);
    }

    /**
     * Hiển thị form đăng ký
     */
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    /**
     * Đăng xuất
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('foods.index')
            ->with('success', 'Đăng xuất thành công');
    }
}