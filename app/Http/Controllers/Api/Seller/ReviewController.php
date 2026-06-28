<?php

namespace App\Http\Controllers\Api\Seller;

use App\Http\Controllers\Controller;
use App\Models\BinhLuan;
use App\Models\NhaHang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function index()
    {
        $sellerId = Auth::user()->MaNguoiDung;
        $restaurant = NhaHang::where('MaNguoiDung', $sellerId)->first();
        
        if (!$restaurant) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy nhà hàng'], 404);
        }

        $reviews = BinhLuan::with(['nguoiDung', 'monAn'])
            ->whereHas('monAn', function($q) use ($restaurant) {
                $q->where('MaNhaHang', $restaurant->MaNhaHang);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $reviews->map(function($review) {
                return [
                    'id' => $review->id,
                    'MaDonHang' => $review->MaDonHang,
                    'TenKhachHang' => $review->nguoiDung->HoTen ?? 'Khách lẻ',
                    'TenMonAn' => $review->monAn->TenMonAn ?? 'Món đã xóa',
                    'DiemDanhGia' => $review->diem_danh_gia,
                    'NoiDung' => $review->noi_dung,
                    'ThoiGian' => $review->created_at->diffForHumans(),
                    'DaPhanHoi' => !empty($review->phan_hoi),
                ];
            })
        ]);
    }
}
