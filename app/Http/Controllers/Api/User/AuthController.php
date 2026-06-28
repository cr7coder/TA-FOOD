<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * API Login
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'login' => 'required|string',
            'password' => 'required|string',
            'device_name' => 'nullable|string',
        ], [
            'login.required' => 'Vui lòng nhập email hoặc tên đăng nhập',
            'password.required' => 'Vui lòng nhập mật khẩu',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        $login = $request->input('login');
        $password = $request->input('password');
        $deviceName = $request->input('device_name', 'api-client');

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
        if (!Hash::check($password, $user->MatKhau)) {
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

        // Đăng nhập session cho web browser
        $guestSessionId = session()->getId();
        $remember = $request->boolean('remember', false);
        Auth::login($user, $remember);

        $user->lan_dang_nhap_cuoi = now();
        $user->save();

        // Merge guest cart items into user account
        $this->mergeGuestCart($user, $guestSessionId);

        // Tạo API token
        $token = $user->createToken($deviceName)->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Đăng nhập thành công',
            'data' => [
                'user' => [
                    'id' => $user->MaNguoiDung,
                    'username' => $user->TenDangNhap,
                    'name' => $user->HoTen,
                    'email' => $user->Email,
                    'phone' => $user->SoDienThoai,
                    'role' => $user->VaiTro,
                ],
                'token' => $token,
                'token_type' => 'Bearer'
            ]
        ], 200);
    }

    /**
     * API Register
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        if (!\App\Services\SettingService::check('app_allow_register', true)) {
            return response()->json([
                'success' => false,
                'message' => 'Đăng ký thành viên mới hiện đang tạm khóa.'
            ], 403);
        }

        // Validate input
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:50|unique:nguoi_dung,TenDangNhap',
            'password' => 'required|string|min:6|confirmed',
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:100|unique:nguoi_dung,Email',
            'phone' => [
                'nullable',
                'string',
                'max:15',
                'digits_between:10,11',
                'regex:/^(02|03|05|07|08|09)\d{8,9}$/',
                'unique:nguoi_dung,SoDienThoai'
            ],
            'role' => 'required|in:KhachHang,NguoiBan',
            'device_name' => 'nullable|string',
        ], [
            'username.required' => 'Vui lòng nhập tên đăng nhập',
            'username.unique' => 'Tên đăng nhập đã tồn tại',
            'password.required' => 'Vui lòng nhập mật khẩu',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp',
            'name.required' => 'Vui lòng nhập họ tên',
            'email.required' => 'Vui lòng nhập email',
            'email.email' => 'Email không hợp lệ',
            'email.unique' => 'Email đã được sử dụng',
            'phone.max' => 'Số điện thoại không được vượt quá 15 ký tự',
            'phone.digits_between' => 'Số điện thoại phải là số từ 10-11 chữ số',
            'phone.regex' => 'Số điện thoại không đúng định dạng',
            'phone.unique' => 'Số điện thoại đã được sử dụng',
            'role.in' => 'Vai trò không hợp lệ',
            'role.required' => 'Vui lòng chọn vai trò',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        // Tạo user mới
        $user = User::create([
            'TenDangNhap' => $request->username,
            'MatKhau' => Hash::make($request->password),
            'HoTen' => $request->name,
            'Email' => $request->email,
            'SoDienThoai' => $request->phone,
            'VaiTro' => $request->role,
        ]);

        // Đăng nhập session cho web browser
        $guestSessionId = session()->getId();
        Auth::login($user, true);

        // Merge guest cart items into new user account
        $this->mergeGuestCart($user, $guestSessionId);

        // Tạo API token
        $deviceName = $request->input('device_name', 'api-client');
        $token = $user->createToken($deviceName)->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Đăng ký tài khoản thành công',
            'data' => [
                'user' => [
                    'id' => $user->MaNguoiDung,
                    'username' => $user->TenDangNhap,
                    'name' => $user->HoTen,
                    'email' => $user->Email,
                    'phone' => $user->SoDienThoai,
                    'role' => $user->VaiTro,
                ],
                'token' => $token,
                'token_type' => 'Bearer'
            ]
        ], 201);
    }

    /**
     * Merge guest cart into user cart.
     * Strategy: if the guest has items, REPLACE the user's existing cart
     * with the current session items (current-session-wins).
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
                    $userCart = \App\Models\GioHang::firstOrCreate(['MaNguoiDung' => $user->MaNguoiDung]);

                    // Clear old items so the current guest session is authoritative
                    \App\Models\GioHangChiTiet::where('MaGioHang', $userCart->MaGioHang)->delete();

                    // Move guest items to user cart
                    foreach ($guestCart->chiTiet as $guestItem) {
                        $guestItem->MaGioHang = $userCart->MaGioHang;
                        $guestItem->save();
                    }

                    $guestCart->delete();
                }
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error merging guest cart in API Auth: ' . $e->getMessage());
        }
    }

    /**
     * API Logout (revoke current token)
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        // Revoke the current user's token
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đăng xuất thành công'
        ], 200);
    }

    /**
     * API Logout All (revoke all tokens)
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logoutAll(Request $request)
    {
        // Revoke all tokens
        $request->user()->tokens()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đã đăng xuất khỏi tất cả các thiết bị'
        ], 200);
    }

    /**
     * Send OTP for Forgot Password
     */
    public function sendForgotPasswordOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Email không hợp lệ'], 422);
        }

        $email = $request->email;
        $user = User::where('Email', $email)->first();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Email không tồn tại trong hệ thống'], 404);
        }

        // Generate 6 digit OTP
        $otp = sprintf("%06d", mt_rand(1, 999999));
        
        // Save to cache for 5 minutes
        \Illuminate\Support\Facades\Cache::put('otp_forgot_pwd_' . $email, $otp, now()->addMinutes(5));

        try {
            \Illuminate\Support\Facades\Mail::to($email)->send(new \App\Mail\ForgotPasswordOtpMail($otp));
            return response()->json([
                'success' => true, 
                'message' => 'Mã OTP đã được gửi vào Email của bạn. Mã có hiệu lực trong 5 phút.'
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Send OTP Email Error: ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => 'Không thể gửi email lúc này. Vui lòng thử lại sau.'
            ], 500);
        }
    }

    /**
     * Verify OTP
     */
    public function verifyForgotPasswordOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'otp' => 'required|digits:6'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Dữ liệu không hợp lệ'], 422);
        }

        $email = $request->email;
        $otp = $request->otp;

        $cachedOtp = \Illuminate\Support\Facades\Cache::get('otp_forgot_pwd_' . $email);

        if (!$cachedOtp) {
            return response()->json(['success' => false, 'message' => 'Mã OTP đã hết hạn hoặc không tồn tại'], 400);
        }

        if ($cachedOtp !== $otp) {
            return response()->json(['success' => false, 'message' => 'Mã OTP không chính xác'], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'Xác thực OTP thành công'
        ]);
    }

    /**
     * Reset Password
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'otp' => 'required|digits:6',
            'password' => 'required|string|min:6|confirmed'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Dữ liệu không hợp lệ', 'errors' => $validator->errors()], 422);
        }

        $email = $request->email;
        $otp = $request->otp;
        $password = $request->password;

        $cachedOtp = \Illuminate\Support\Facades\Cache::get('otp_forgot_pwd_' . $email);

        if (!$cachedOtp || $cachedOtp !== $otp) {
            return response()->json(['success' => false, 'message' => 'OTP không hợp lệ hoặc đã hết hạn'], 400);
        }

        $user = User::where('Email', $email)->first();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Người dùng không tồn tại'], 404);
        }

        // Update password
        $user->MatKhau = Hash::make($password);
        $user->save();

        // Clear OTP from cache
        \Illuminate\Support\Facades\Cache::forget('otp_forgot_pwd_' . $email);

        return response()->json([
            'success' => true,
            'message' => 'Mật khẩu đã được cập nhật thành công! Bạn có thể đăng nhập ngay bây giờ.'
        ]);
    }
}

