<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BinhLuan;
use App\Models\DonHang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
{
    /**
     * Tạo đánh giá món ăn
     * POST /api/reviews
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|integer|exists:don_hang,MaDonHang',
            'food_id' => 'required|integer|exists:mon_an,MaMonAn',
            'rating' => 'required|integer|min:1|max:5',
            'content' => 'required|string|max:1000',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
        ], [
            'order_id.required' => 'Mã đơn hàng không được để trống',
            'order_id.exists' => 'Đơn hàng không tồn tại',
            'food_id.required' => 'Mã món ăn không được để trống',
            'food_id.exists' => 'Món ăn không tồn tại',
            'rating.required' => 'Vui lòng chọn số sao đánh giá',
            'rating.min' => 'Điểm đánh giá phải từ 1 đến 5 sao',
            'rating.max' => 'Điểm đánh giá phải từ 1 đến 5 sao',
            'content.required' => 'Vui lòng nhập nội dung đánh giá',
            'content.max' => 'Nội dung đánh giá không được vượt quá 1000 ký tự',
            'image.image' => 'File tải lên phải là hình ảnh',
            'image.mimes' => 'Hình ảnh chỉ hỗ trợ định dạng jpg, jpeg, png',
            'image.max' => 'Kích thước hình ảnh không được vượt quá 5MB',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        // Kiểm tra đơn hàng có thuộc về user hiện tại không
        $order = DonHang::with(['chiTiet'])
            ->where('MaDonHang', $request->order_id)
            ->where('MaNguoiDung', Auth::id())
            ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Đơn hàng không tồn tại hoặc không thuộc về bạn',
            ], 403);
        }

        // Kiểm tra món ăn có trong đơn hàng không
        $foodInOrder = $order->chiTiet->where('MaMonAn', $request->food_id)->first();
        if (!$foodInOrder) {
            return response()->json([
                'success' => false,
                'message' => 'Món ăn không có trong đơn hàng này',
            ], 400);
        }

        // Kiểm tra đã đánh giá chưa
        $existingReview = BinhLuan::where('ma_nguoi_dung', Auth::id())
            ->where('ma_mon_an', $request->food_id)
            ->where('ma_don_hang', $request->order_id)
            ->first();

        if ($existingReview) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn đã đánh giá món ăn này rồi',
            ], 400);
        }

        $data = [
            'ma_mon_an' => $request->food_id,
            'ma_nguoi_dung' => Auth::id(),
            'ma_don_hang' => $request->order_id,
            'diem_danh_gia' => $request->rating,
            'noi_dung' => $request->content,
            'da_mua' => true,
            'trang_thai' => 'Chờ duyệt',
        ];

        // Upload hình ảnh nếu có
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/reviews', $fileName);
            $data['hinh_anh'] = $fileName;
        }

        $review = BinhLuan::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Đánh giá món ăn thành công',
            'data' => [
                'id' => $review->id,
                'order_id' => $review->ma_don_hang,
                'food_id' => $review->ma_mon_an,
                'rating' => $review->diem_danh_gia,
                'content' => $review->noi_dung,
                'image' => $review->hinh_anh ? asset('storage/reviews/' . $review->hinh_anh) : null,
                'status' => $review->trang_thai,
                'created_at' => $review->created_at->format('Y-m-d H:i:s'),
            ]
        ], 201);
    }

    /**
     * Kiểm tra đã đánh giá chưa
     * GET /api/reviews/check/{orderId}/{foodId}
     */
    public function checkReviewed($orderId, $foodId)
    {
        $hasReviewed = BinhLuan::where('ma_nguoi_dung', Auth::id())
            ->where('ma_mon_an', $foodId)
            ->where('ma_don_hang', $orderId)
            ->exists();

        return response()->json([
            'success' => true,
            'data' => [
                'has_reviewed' => $hasReviewed
            ]
        ], 200);
    }

    /**
     * Lấy danh sách đánh giá của user
     * GET /api/reviews
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        
        $reviews = BinhLuan::with(['monAn', 'nguoiDung'])
            ->where('ma_nguoi_dung', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Lấy danh sách đánh giá thành công',
            'data' => $reviews->map(function($review) {
                return [
                    'id' => $review->id,
                    'order_id' => $review->ma_don_hang,
                    'food_id' => $review->ma_mon_an,
                    'food_name' => $review->monAn->TenMonAn,
                    'food_image' => $review->monAn->HinhAnh ? asset('images/' . $review->monAn->HinhAnh) : null,
                    'rating' => $review->diem_danh_gia,
                    'content' => $review->noi_dung,
                    'image' => $review->hinh_anh ? asset('storage/reviews/' . $review->hinh_anh) : null,
                    'status' => $review->trang_thai,
                    'created_at' => $review->created_at->format('Y-m-d H:i:s'),
                ];
            }),
            'pagination' => [
                'total' => $reviews->total(),
                'per_page' => $reviews->perPage(),
                'current_page' => $reviews->currentPage(),
                'last_page' => $reviews->lastPage(),
            ]
        ], 200);
    }
}
