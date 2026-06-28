<?php

namespace App\Http\Controllers\Web\Auth;

use App\Http\Controllers\Controller;

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
        if (!\App\Services\SettingService::check('app_allow_register', true)) {
            return response()->json([
                'success' => false,
                'message' => 'Đăng ký thành viên mới hiện đang tạm khóa.'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'username' => 'required|string|unique:nguoi_dung,TenDangNhap',
            'fullname' => 'required|string',
            'email' => 'required|email|unique:nguoi_dung,Email',
            'password' => 'required|string|min:6|confirmed',
            'phone' => [
                'nullable',
                'string',
                'max:15',
                'digits_between:10,11',
                'regex:/^(02|03|05|07|08|09)\d{8,9}$/',
                'unique:nguoi_dung,SoDienThoai'
            ],
        ], [
            'username.required' => 'Vui lòng nhập tên đăng nhập',
            'fullname.required' => 'Vui lòng nhập họ và tên',
            'email.required' => 'Vui lòng nhập email',
            'email.email' => 'Email không hợp lệ',
            'email.unique' => 'Email đã tồn tại',
            'phone.max' => 'Số điện thoại không được vượt quá 15 ký tự',
            'phone.digits_between' => 'Số điện thoại phải là số từ 10-11 chữ số',
            'phone.regex' => 'Số điện thoại không đúng định dạng',
            'phone.unique' => 'Số điện thoại đã tồn tại',
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
        $user->VaiTro = 'KhachHang'; // Vai trò mặc định cho người dùng mới
        $user->save();

        $guestSessionId = session()->getId();
        Auth::login($user);

        $this->mergeGuestCart($user, $guestSessionId);

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
     * Merge guest cart into user cart.
     * Strategy: if the guest has items in their cart, REPLACE the user's existing
     * cart entirely with the guest cart items (current-session-wins).
     * This avoids accumulating items across multiple sessions.
     */
    private function mergeGuestCart($user, $guestSessionId = null)
    {
        try {
            if (!$guestSessionId) {
                $guestSessionId = session()->getId();
            }
            if ($guestSessionId) {
                $guestCart = \App\Models\GioHang::where('session_id', $guestSessionId)->first();
                if ($guestCart && $guestCart->chiTiet->count() > 0) {
                    // Get or create user cart
                    $userCart = \App\Models\GioHang::firstOrCreate(['MaNguoiDung' => $user->MaNguoiDung]);

                    // Clear old items from the user's cart so current session wins
                    \App\Models\GioHangChiTiet::where('MaGioHang', $userCart->MaGioHang)->delete();

                    // Move all guest cart items into the user's cart
                    foreach ($guestCart->chiTiet as $guestItem) {
                        $guestItem->MaGioHang = $userCart->MaGioHang;
                        $guestItem->save();
                    }

                    // Delete the now-empty guest cart row
                    $guestCart->delete();
                }
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error merging guest cart: ' . $e->getMessage());
        }
    }

    /**
     * Hiển thị form đăng nhập
     */
    public function showLoginForm()
    {
        // Nếu đã đăng nhập, redirect về trang chủ tương ứng với vai trò
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->isAdmin()) {
                return redirect()->route('admin.dashboard');
            } elseif ($user->isSeller()) {
                return redirect()->route('seller.dashboard');
            }
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

        // Kiểm tra trạng thái tài khoản
        if ($user->TrangThai && $user->TrangThai !== 'Hoạt động') {
            return response()->json([
                'success' => false,
                'message' => 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ Admin.'
            ], 403);
        }

        // Đăng nhập thành công
        $guestSessionId = session()->getId();
        Auth::login($user, $remember);

        $user->lan_dang_nhap_cuoi = now();
        $user->save();

        $this->mergeGuestCart($user, $guestSessionId);

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
            'redirect' => $this->redirectAfterLogin($user, $request)
        ]);
    }

    /**
     * Hiển thị form đăng ký
     */
    public function showRegisterForm()
    {
        if (!\App\Services\SettingService::check('app_allow_register', true)) {
            return redirect()->route('login')->with('error', 'Đăng ký thành viên mới hiện đang tạm khóa.');
        }

        return view('auth.register');
    }

    /**
     * Đăng xuất
     */
    public function logout(Request $request)
    {
        $user = Auth::user();

        // Xóa giỏ hàng của user trong DB khi đăng xuất để phiên sau bắt đầu sạch
        if ($user) {
            try {
                $userCart = \App\Models\GioHang::where('MaNguoiDung', $user->MaNguoiDung)->first();
                if ($userCart) {
                    \App\Models\GioHangChiTiet::where('MaGioHang', $userCart->MaGioHang)->delete();
                    $userCart->delete();
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Error clearing user cart on logout: ' . $e->getMessage());
            }
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('foods.index')
            ->with('toast_success', 'Đăng xuất thành công');
    }

    /**
     * Chuyển hướng sang Google Auth hoặc Mock Auth
     */
    public function redirectToGoogle()
    {
        if (empty(config('services.google.client_id')) || empty(config('services.google.client_secret'))) {
            // Chế độ giả lập khi chạy local chưa cấu hình
            return redirect()->route('auth.google.mock');
        }

        return \Laravel\Socialite\Facades\Socialite::driver('google')->redirect();
    }

    /**
     * Xử lý callback từ Google Auth thực tế
     */
    public function handleGoogleCallback(Request $request)
    {
        try {
            $googleUser = \Laravel\Socialite\Facades\Socialite::driver('google')->user();
            return $this->loginOrCreateGoogleUser(
                $googleUser->getId(),
                $googleUser->getEmail(),
                $googleUser->getName(),
                $googleUser->getAvatar()
            );
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Google Auth Error: ' . $e->getMessage());
            return redirect()->route('login')->with('error', 'Đăng nhập bằng Google thất bại: ' . $e->getMessage());
        }
    }

    /**
     * Trang giả lập đăng nhập Google
     */
    public function showMockGoogleLogin()
    {
        if (Auth::check()) {
            return redirect()->route('foods.index');
        }
        return view('auth.google-mock');
    }

    /**
     * Xử lý giả lập đăng nhập Google
     */
    public function handleMockGoogleCallback(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'name' => 'required|string|max:100',
        ], [
            'email.required' => 'Vui lòng nhập email',
            'email.email' => 'Email không đúng định dạng',
            'name.required' => 'Vui lòng nhập họ tên',
        ]);

        $email = $request->input('email');
        $name = $request->input('name');
        $googleId = 'mock_google_' . md5($email);
        $mockAvatar = 'https://ui-avatars.com/api/?name=' . urlencode($name) . '&background=ffbe33&color=222831&size=128&bold=true';

        return $this->loginOrCreateGoogleUser($googleId, $email, $name, $mockAvatar);
    }

    /**
     * Helper dùng chung cho login hoặc tạo tài khoản Google
     */
    private function loginOrCreateGoogleUser($googleId, $email, $name, $avatarUrl = null)
    {
        // 1. Tìm user theo google_id
        $user = User::where('google_id', $googleId)->first();

        if (!$user) {
            // 2. Tìm user theo Email
            $user = User::where('Email', $email)->first();

            if ($user) {
                // Liên kết tài khoản sẵn có với google_id
                $user->google_id = $googleId;
                if ($avatarUrl) {
                    $user->AnhDaiDien = $avatarUrl;
                }
                $user->save();
            } else {
                // 3. Tạo mới tài khoản
                $emailPrefix = explode('@', $email)[0];
                $username = $emailPrefix;
                // Đảm bảo username là duy nhất
                $count = 1;
                while (User::where('TenDangNhap', $username)->exists()) {
                    $username = $emailPrefix . $count;
                    $count++;
                }

                $user = new User();
                $user->TenDangNhap = $username;
                $user->HoTen = $name;
                $user->Email = $email;
                $user->google_id = $googleId;
                $user->AnhDaiDien = $avatarUrl;
                $user->MatKhau = bcrypt(\Illuminate\Support\Str::random(24));
                $user->VaiTro = 'KhachHang';
                $user->TrangThai = 'Hoạt động';
                $user->save();
            }
        } else {
            // Cập nhật lại avatar nếu thay đổi từ Google
            if ($avatarUrl && $user->AnhDaiDien !== $avatarUrl) {
                $user->AnhDaiDien = $avatarUrl;
                $user->save();
            }
        }

        // Kiểm tra trạng thái tài khoản
        if ($user->TrangThai && $user->TrangThai !== 'Hoạt động') {
            return redirect()->route('login')->with('error', 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ Admin.');
        }

        // Đăng nhập thành công
        $guestSessionId = session()->getId();
        Auth::login($user, true);

        $user->lan_dang_nhap_cuoi = now();
        $user->save();

        $this->mergeGuestCart($user, $guestSessionId);

        // Lưu user vào localStorage ở frontend
        session()->flash('google_login_success', [
            'id' => $user->MaNguoiDung,
            'name' => $user->HoTen,
            'email' => $user->Email,
            'role' => $user->VaiTro,
        ]);

        return redirect()->route('foods.index')->with('toast_success', 'Đăng nhập thành công!');
    }
}
