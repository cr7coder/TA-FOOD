<?php

namespace App\Http\Controllers\Api\User;

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
            'images' => 'nullable|array|max:5',
            'images.*' => 'nullable|file|mimes:jpg,jpeg,png,mp4,mov,avi|max:20480',
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
            'images.max' => 'Chỉ được tải lên tối đa 5 tệp',
            'images.*.mimes' => 'Chỉ hỗ trợ định dạng ảnh (jpg, jpeg, png) hoặc video (mp4, mov, avi)',
            'images.*.max' => 'Kích thước mỗi tệp không được vượt quá 20MB',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        // Kiểm tra từ ngữ cấm (đơn giản)
        $bannedWords = ['tục tĩu', 'ngu', 'chết', 'đồ chó', 'súc vật'];
        $noiDung = strtolower($request->content);
        foreach ($bannedWords as $word) {
            if (strpos($noiDung, $word) !== false) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nội dung đánh giá có chứa từ ngữ không phù hợp',
                    'errors' => [
                        'content' => ['Nội dung đánh giá có chứa từ ngữ không phù hợp']
                    ]
                ], 422);
            }
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

        // Kiểm tra trạng thái đơn hàng phải là Hoàn thành
        if ($order->TrangThai !== 'Hoàn thành') {
            return response()->json([
                'success' => false,
                'message' => 'Bạn chỉ có thể đánh giá đơn hàng ở trạng thái Hoàn thành',
            ], 400);
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
        $existingReview = BinhLuan::where('MaNguoiDung', Auth::id())
            ->where('MaMonAn', $request->food_id)
            ->where('MaDonHang', $request->order_id)
            ->first();

        if ($existingReview) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn đã đánh giá món ăn này rồi',
            ], 400);
        }

        $data = [
            'MaMonAn' => $request->food_id,
            'MaNguoiDung' => Auth::id(),
            'MaDonHang' => $request->order_id,
            'diem_danh_gia' => $request->rating,
            'noi_dung' => $request->content,
            'da_mua' => true,
            'trang_thai' => 'Đã duyệt',
        ];

        // Upload nhiều hình ảnh/video nếu có
        $uploadedFiles = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                 $uploadedFileUrl = \CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary::uploadApi()->upload(
                    $file->getRealPath(),
                    [
                        'folder' => 'ta-food/reviews',
                        'resource_type' => 'auto'
                    ]
                )['secure_url'];
                $uploadedFiles[] = $uploadedFileUrl;
            }
            $data['hinh_anh'] = json_encode($uploadedFiles);
        }

        $review = BinhLuan::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Đánh giá món ăn thành công',
            'data' => [
                'id' => $review->id,
                'order_id' => $review->MaDonHang,
                'food_id' => $review->MaMonAn,
                'rating' => $review->diem_danh_gia,
                'content' => $review->noi_dung,
                'images' => $review->hinh_anh ? array_map(function($path) { 
                    return filter_var($path, FILTER_VALIDATE_URL) ? $path : asset('images/' . $path); 
                }, json_decode($review->hinh_anh, true) ?? []) : [],
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
        $hasReviewed = BinhLuan::where('MaNguoiDung', Auth::id())
            ->where('MaMonAn', $foodId)
            ->where('MaDonHang', $orderId)
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
            ->where('MaNguoiDung', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Lấy danh sách đánh giá thành công',
            'data' => $reviews->map(function($review) {
                return [
                    'id' => $review->id,
                    'order_id' => $review->MaDonHang,
                    'food_id' => $review->MaMonAn,
                    'food_name' => $review->monAn->TenMonAn,
                    'food_image' => $review->monAn ? $review->monAn->hinh_anh_url : null,
                    'rating' => $review->diem_danh_gia,
                    'content' => $review->noi_dung,
                    'images' => $review->hinh_anh ? array_map(function($path) { 
                        return filter_var($path, FILTER_VALIDATE_URL) ? $path : asset('images/' . $path); 
                    }, json_decode($review->hinh_anh, true) ?? []) : [],
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

