<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    // Check if user is admin
    private function checkAdmin()
    {
        if (!auth()->check() || auth()->user()->VaiTro !== 'QuanTri') {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền truy cập.'
            ], 403);
        }
        return null;
    }

    public function index(Request $request)
    {
        if ($response = $this->checkAdmin()) return $response;

        $query = User::query();

        // Search
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('HoTen', 'like', "%$search%")
                  ->orWhere('Email', 'like', "%$search%")
                  ->orWhere('SoDienThoai', 'like', "%$search%");
            });
        }

        // Filter by role
        if ($request->has('role') && $request->role != '') {
            $query->where('VaiTro', $request->role);
        }

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('TrangThai', $request->status);
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(10);

        // Calculate orders and spending
        $users->getCollection()->transform(function ($user) {
            $user->tong_don = $user->donHang()->count();
            $user->chi_tieu = $user->donHang()->sum('TongTien');
            return $user;
        });

        return response()->json([
            'success' => true,
            'data' => $users->items(),
            'pagination' => [
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total()
            ]
        ]);
    }

    public function stats()
    {
        if ($response = $this->checkAdmin()) return $response;

        $total = User::count();
        $admin = User::where('VaiTro', 'QuanTri')->count();
        $seller = User::where('VaiTro', 'NguoiBan')->count();
        $customer = User::where('VaiTro', 'KhachHang')->count();

        return response()->json([
            'success' => true,
            'data' => [
                'total' => $total,
                'admin' => $admin,
                'seller' => $seller,
                'customer' => $customer
            ]
        ]);
    }

    public function store(Request $request)
    {
        if ($response = $this->checkAdmin()) return $response;

        $request->validate([
            'TenDangNhap' => 'required|unique:nguoi_dung,TenDangNhap',
            'MatKhau' => ['required', 'min:6', 'regex:/^(?=.*[A-Z])(?=.*[!@#$%^&*(),.?":{}|<>]).*$/'],
            'HoTen' => 'required|min:2',
            'Email' => 'required|email|unique:nguoi_dung,Email',
            'SoDienThoai' => [
                'required',
                'max:15',
                'digits_between:10,11',
                'regex:/^(02|03|05|07|08|09)\d{8,9}$/',
                'unique:nguoi_dung,SoDienThoai'
            ],
            'VaiTro' => 'required|in:QuanTri,NguoiBan,KhachHang',
        ], [
            'TenDangNhap.required' => 'Tên đăng nhập không được bỏ trống',
            'TenDangNhap.unique' => 'Tên đăng nhập đã tồn tại',
            'MatKhau.required' => 'Mật khẩu không được bỏ trống',
            'MatKhau.min' => 'Mật khẩu phải có ít nhất 6 ký tự',
            'MatKhau.regex' => 'Mật khẩu từ 6 ký tự, chứa ít nhất 1 chữ cái viết hoa và 1 ký tự đặc biệt',
            'HoTen.required' => 'Họ và tên không được bỏ trống',
            'HoTen.min' => 'Họ tên phải chứa ít nhất 2 ký tự',
            'Email.required' => 'Email không được bỏ trống',
            'Email.email' => 'Email không đúng định dạng',
            'Email.unique' => 'Email này đã được sử dụng bởi người dùng khác',
            'SoDienThoai.required' => 'Số điện thoại không được bỏ trống',
            'SoDienThoai.max' => 'Số điện thoại không được vượt quá 15 ký tự',
            'SoDienThoai.digits_between' => 'Số điện thoại phải là số từ 10-11 chữ số',
            'SoDienThoai.regex' => 'Số điện thoại không đúng định dạng',
            'SoDienThoai.unique' => 'Số điện thoại này đã được sử dụng bởi người dùng khác',
            'VaiTro.required' => 'Vai trò không được bỏ trống',
            'VaiTro.in' => 'Vai trò không hợp lệ',
        ]);

        $user = new User();
        $user->TenDangNhap = $request->TenDangNhap;
        $user->MatKhau = bcrypt($request->MatKhau);
        $user->HoTen = $request->HoTen;
        $user->Email = $request->Email;
        $user->SoDienThoai = $request->SoDienThoai;
        $user->VaiTro = $request->VaiTro;
        $user->TrangThai = 'Hoạt động';

        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Tạo người dùng thành công.',
            'data' => $user
        ], 201);
    }

    public function show($id)
    {
        if ($response = $this->checkAdmin()) return $response;

        $user = User::where('MaNguoiDung', $id)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy người dùng.'
            ], 404);
        }

        $user->tong_don = $user->donHang()->count();
        $user->chi_tieu = $user->donHang()->sum('TongTien');
        $user->ngay_tham_gia = $user->created_at ? $user->created_at->format('d/m/Y') : '01/01/2024';
        $user->lan_dang_nhap_cuoi = $user->lan_dang_nhap_cuoi ? \Carbon\Carbon::parse($user->lan_dang_nhap_cuoi)->format('d/m/Y H:i') : 'Chưa đăng nhập';

        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }

    public function toggleStatus($id)
    {
        if ($response = $this->checkAdmin()) return $response;

        $user = User::where('MaNguoiDung', $id)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy người dùng.'
            ], 404);
        }

        if ($user->TrangThai === 'Hoạt động') {
            $user->TrangThai = 'Bị khóa';
            $message = 'Đã vô hiệu hóa tài khoản.';
        } else {
            $user->TrangThai = 'Hoạt động';
            $message = 'Đã kích hoạt lại tài khoản.';
        }

        $user->save();

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => [
                'TrangThai' => $user->TrangThai
            ]
        ]);
    }

    public function update(Request $request, $id)
    {
        if ($response = $this->checkAdmin()) return $response;

        $user = User::where('MaNguoiDung', $id)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy người dùng.'
            ], 404);
        }

        $request->validate([
            'TenDangNhap' => 'required|unique:nguoi_dung,TenDangNhap,' . $id . ',MaNguoiDung',
            'HoTen' => 'required|min:2',
            'Email' => 'required|email|unique:nguoi_dung,Email,' . $id . ',MaNguoiDung',
            'SoDienThoai' => [
                'required',
                'max:15',
                'digits_between:10,11',
                'regex:/^(02|03|05|07|08|09)\d{8,9}$/',
                'unique:nguoi_dung,SoDienThoai,' . $id . ',MaNguoiDung'
            ],
            'VaiTro' => 'required|in:QuanTri,NguoiBan,KhachHang',
            'MatKhau' => ['nullable', 'min:6', 'regex:/^(?=.*[A-Z])(?=.*[!@#$%^&*(),.?":{}|<>]).*$/'],
        ], [
            'TenDangNhap.required' => 'Tên đăng nhập không được bỏ trống',
            'TenDangNhap.unique' => 'Tên đăng nhập đã tồn tại',
            'HoTen.required' => 'Họ và tên không được bỏ trống',
            'HoTen.min' => 'Họ tên phải chứa ít nhất 2 ký tự',
            'Email.required' => 'Email không được bỏ trống',
            'Email.email' => 'Email không đúng định dạng',
            'Email.unique' => 'Email này đã được sử dụng bởi người dùng khác',
            'SoDienThoai.required' => 'Số điện thoại không được bỏ trống',
            'SoDienThoai.max' => 'Số điện thoại không được vượt quá 15 ký tự',
            'SoDienThoai.digits_between' => 'Số điện thoại phải là số từ 10-11 chữ số',
            'SoDienThoai.regex' => 'Số điện thoại không đúng định dạng',
            'SoDienThoai.unique' => 'Số điện thoại này đã được sử dụng bởi người dùng khác',
            'VaiTro.required' => 'Vai trò không được bỏ trống',
            'VaiTro.in' => 'Vai trò không hợp lệ',
            'MatKhau.min' => 'Mật khẩu phải có ít nhất 6 ký tự',
            'MatKhau.regex' => 'Mật khẩu từ 6 ký tự, chứa ít nhất 1 chữ cái viết hoa và 1 ký tự đặc biệt',
        ]);

        $user->TenDangNhap = $request->TenDangNhap;
        if ($request->filled('MatKhau')) {
            $user->MatKhau = bcrypt($request->MatKhau);
        }
        $user->HoTen = $request->HoTen;
        $user->Email = $request->Email;
        $user->SoDienThoai = $request->SoDienThoai;
        $user->VaiTro = $request->VaiTro;

        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật người dùng thành công.',
            'data' => $user
        ]);
    }

    public function destroy($id)
    {
        if ($response = $this->checkAdmin()) return $response;

        $user = User::where('MaNguoiDung', $id)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy người dùng.'
            ], 404);
        }

        try {
            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'Xóa người dùng thành công!'
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
