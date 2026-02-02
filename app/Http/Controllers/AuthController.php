<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Xử lý đăng ký
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|unique:nguoi_dung,TenDangNhap',
            'fullname' => 'required|string',
            'email' => 'required|email|unique:nguoi_dung,Email',
            'password' => 'required|string|min:6|confirmed',
            'phone' => 'nullable|string',
        ], [
            'username.required' => 'Vui lòng nhập tên đăng nhập',
            'fullname.required' => 'Vui lòng nhập họ và tên',
            'email.required' => 'Vui lòng nhập email',
            'email.email' => 'Email không hợp lệ',
            'email.unique' => 'Email đã tồn tại',
            'password.required' => 'Vui lòng nhập mật khẩu',
            'password.min' => 'Mật khẩu tối thiểu 6 ký tự',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = new User();
        $user->TenDangNhap = $request->input('username');
        $user->HoTen = $request->input('fullname');
        $user->Email = $request->input('email');
        $user->MatKhau = bcrypt($request->input('password'));
        $user->SoDienThoai = $request->input('phone');
        $user->VaiTro = 'user'; // Hoặc xác định vai trò theo logic của bạn
        $user->save();

        Auth::login($user);

        $redirect = $this->redirectAfterLogin($user, $request);

        return response()->json([
            'success' => true,
            'message' => 'Đăng ký thành công',
            'user' => [
                'id' => $user->MaNguoiDung,
                'name' => $user->HoTen,
                'email' => $user->Email,
                'role' => $user->VaiTro,
            ],
            'redirect' => $redirect
        ]);
    }

    /**
     * Chuyển hướng sau đăng nhập/đăng ký theo role
     */
    private function redirectAfterLogin(User $user, ?Request $request = null): string
    {
        if (method_exists($user, 'isAdmin') && $user->isAdmin()) {
            return route('admin.vouchers.index');
        }

        if (method_exists($user, 'isSeller') && $user->isSeller()) {
            return route('seller.foods.index');
        }

        if ($request) {
            $intended = $request->session()->pull('url.intended');
            if ($intended && !str_contains($intended, '/admin')) {
                return $intended;
            }
        }

        return route('foods.index');
    }

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