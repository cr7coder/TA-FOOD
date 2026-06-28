<?php

namespace App\Http\Controllers\Web\Client;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\DonHang;
use App\Models\BinhLuan;

class OrderHistoryController extends Controller
{
    /**
     * Hiển thị lịch sử đơn hàng của người dùng
     */
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để xem lịch sử đơn hàng');
        }

        $donHangs = DonHang::with(['chiTiet.monAn', 'binhLuans'])
            ->where('MaNguoiDung', Auth::user()->MaNguoiDung)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('client.orders.history', compact('donHangs'));
    }

    /**
     * Hiển thị chi tiết đơn hàng
     */
    public function show($id)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để xem chi tiết đơn hàng');
        }

        $donHang = DonHang::with(['chiTiet.monAn', 'binhLuans.monAn', 'giamGia', 'thanhToan'])
            ->where('MaDonHang', $id)
            ->where('MaNguoiDung', Auth::user()->MaNguoiDung)
            ->firstOrFail();

        return view('client.orders.detail', compact('donHang'));
    }
}

