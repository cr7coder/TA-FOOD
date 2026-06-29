<?php

namespace App\Http\Controllers\Web\Seller;

use App\Http\Controllers\Controller;
use App\Models\BinhLuan;
use App\Models\NhaHang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ReviewController extends Controller
{
    public function index()
    {
        $sellerId = Auth::user()->MaNguoiDung;
        $restaurant = NhaHang::where('MaNguoiDung', $sellerId)->first();
        
        if (!$restaurant) {
            return redirect()->back()->with('error', 'Không tìm thấy nhà hàng.');
        }

        $reviews = BinhLuan::with(['nguoiDung', 'monAn'])
            ->whereHas('monAn', function($q) use ($restaurant) {
                $q->where('MaNhaHang', $restaurant->MaNhaHang);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('seller.reviews.index', compact('reviews'));
    }

    public function reply(Request $request, $id)
    {
        $request->validate([
            'phan_hoi' => 'required|string|max:1000',
        ]);

        $sellerId = Auth::user()->MaNguoiDung;
        $restaurant = NhaHang::where('MaNguoiDung', $sellerId)->first();

        $review = BinhLuan::whereHas('monAn', function($q) use ($restaurant) {
            $q->where('MaNhaHang', $restaurant->MaNhaHang);
        })->findOrFail($id);

        $review->update([
            'phan_hoi' => $request->phan_hoi,
            'phan_hoi_at' => Carbon::now('Asia/Ho_Chi_Minh'),
        ]);

        // Gửi thông báo đến Khách hàng (User) khi Seller phản hồi đánh giá
        if ($review->MaNguoiDung) {
            $tenNhaHang = $restaurant->TenNhaHang ?? 'Cửa hàng';
            $tenMonAn = $review->monAn->TenMonAn ?? 'món ăn';
            \App\Services\NotificationService::add(
                $review->MaNguoiDung,
                '💬 Nhà hàng đã phản hồi đánh giá của bạn!',
                "Cửa hàng \"{$tenNhaHang}\" vừa phản hồi đánh giá của bạn về món \"{$tenMonAn}\".",
                $review->MaDonHang
            );
        }

        return redirect()->back()->with('success', 'Đã gửi phản hồi thành công.');
    }
}
