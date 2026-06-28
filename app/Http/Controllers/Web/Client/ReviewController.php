<?php

namespace App\Http\Controllers\Web\Client;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Models\BinhLuan;
use App\Models\DonHang;
use App\Models\MonAn;

class ReviewController extends Controller
{
    /**
     * Hiển thị form đánh giá món ăn
     */
    public function create($maDonHang, $maMonAn)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để đánh giá');
        }

        // Kiểm tra đơn hàng có tồn tại và thuộc về user hiện tại
        $donHang = DonHang::with(['chiTiet.monAn'])
            ->where('MaDonHang', $maDonHang)
            ->where('MaNguoiDung', Auth::user()->MaNguoiDung)
            ->firstOrFail();

        // Kiểm tra món ăn có trong đơn hàng không
        $monAnInOrder = $donHang->chiTiet->where('MaMonAn', $maMonAn)->first();
        if (!$monAnInOrder) {
            return redirect()->back()->with('error', 'Món ăn không có trong đơn hàng này');
        }

        $monAn = $monAnInOrder->monAn;

        return view('client.reviews.create', compact('donHang', 'monAn', 'maDonHang', 'maMonAn'));
    }

    /**
     * Lưu đánh giá món ăn
     */
    public function store(Request $request, $maDonHang, $maMonAn)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để đánh giá');
        }

        // Kiểm tra đơn hàng có tồn tại và thuộc về user hiện tại
        $donHang = DonHang::with(['chiTiet'])
            ->where('MaDonHang', $maDonHang)
            ->where('MaNguoiDung', Auth::user()->MaNguoiDung)
            ->firstOrFail();

        // Kiểm tra đơn hàng đã hoàn thành chưa
        if (!$donHang->canReview()) {
            return redirect()->back()->with('error', 'Chỉ có thể đánh giá đơn hàng đã hoàn thành');
        }

        // Kiểm tra món ăn có trong đơn hàng không
        $monAnInOrder = $donHang->chiTiet->where('MaMonAn', $maMonAn)->first();
        if (!$monAnInOrder) {
            return redirect()->back()->with('error', 'Món ăn không có trong đơn hàng này');
        }

        // Kiểm tra đã đánh giá chưa
        if (BinhLuan::hasReviewed(Auth::user()->MaNguoiDung, $maMonAn, $maDonHang)) {
            return redirect()->back()->with('error', 'Bạn đã đánh giá món ăn này rồi');
        }

        // Validation
        $validator = Validator::make($request->all(), [
            'diem_danh_gia' => 'required|integer|min:1|max:5',
            'noi_dung' => 'required|string|max:1000',
            'hinh_anh' => 'nullable|image|mimes:jpg,jpeg,png|max:5120', // 5MB
            'video' => 'nullable|mimes:mp4,mov|max:20480' // 20MB
        ], [
            'diem_danh_gia.required' => 'Vui lòng chọn số sao đánh giá',
            'diem_danh_gia.min' => 'Điểm đánh giá phải từ 1 đến 5 sao',
            'diem_danh_gia.max' => 'Điểm đánh giá phải từ 1 đến 5 sao',
            'noi_dung.required' => 'Vui lòng nhập nội dung đánh giá',
            'noi_dung.max' => 'Nội dung đánh giá không được vượt quá 1000 ký tự',
            'hinh_anh.image' => 'File tải lên phải là hình ảnh',
            'hinh_anh.mimes' => 'Hình ảnh chỉ hỗ trợ định dạng jpg, jpeg, png',
            'hinh_anh.max' => 'Kích thước hình ảnh không được vượt quá 5MB',
            'video.mimes' => 'Video chỉ hỗ trợ định dạng mp4, mov',
            'video.max' => 'Kích thước video không được vượt quá 20MB'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Kiểm tra từ ngữ cấm (đơn giản)
        $bannedWords = ['tục tĩu', 'ngu', 'chết', 'đồ chó', 'súc vật']; // Có thể mở rộng thêm
        $noiDung = strtolower($request->noi_dung);
        foreach ($bannedWords as $word) {
            if (strpos($noiDung, $word) !== false) {
                return redirect()->back()
                    ->withErrors(['noi_dung' => 'Nội dung đánh giá có chứa từ ngữ không phù hợp'])
                    ->withInput();
            }
        }

        $data = [
            'MaMonAn' => $maMonAn,
            'MaNguoiDung' => Auth::user()->MaNguoiDung,
            'MaDonHang' => $maDonHang,
            'diem_danh_gia' => $request->diem_danh_gia,
            'noi_dung' => $request->noi_dung
        ];

        // Upload hình ảnh nếu có
        if ($request->hasFile('hinh_anh')) {
            $file = $request->file('hinh_anh');
            $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('images/reviews'), $fileName);
            $data['hinh_anh'] = 'reviews/' . $fileName;
        }

        // Upload video nếu có
        if ($request->hasFile('video')) {
            $file = $request->file('video');
            $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('images/reviews'), $fileName);
            $data['video'] = 'reviews/' . $fileName;
        }

        // Tạo đánh giá
        BinhLuan::create($data);

        return redirect()->route('orders.show', $maDonHang)
            ->with('success', 'Đánh giá món ăn thành công');
    }
}

