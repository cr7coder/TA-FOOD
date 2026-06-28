<?php

namespace App\Http\Controllers\Api\Seller;

use App\Http\Controllers\Controller;
use App\Models\NhaHang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RestaurantController extends Controller
{
    /**
     * Get current seller's restaurant info
     */
    public function show()
    {
        $sellerId = Auth::user()->MaNguoiDung;
        $restaurant = NhaHang::where('MaNguoiDung', $sellerId)->first();

        if (!$restaurant) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy thông tin nhà hàng'
            ], 404);
        }

        $restaurant->append('hinh_anh_url');

        return response()->json([
            'success' => true,
            'data' => $restaurant
        ]);
    }

    /**
     * Update seller's restaurant profile
     */
    public function update(Request $request)
    {
        $sellerId = Auth::user()->MaNguoiDung;
        $restaurant = NhaHang::where('MaNguoiDung', $sellerId)->first();

        $isNew = false;
        if (!$restaurant) {
            $restaurant = new NhaHang();
            $restaurant->MaNguoiDung = $sellerId;
            $isNew = true;
        }

        $validated = $request->validate([
            'TenNhaHang'  => ['required', 'string', 'max:100', 'regex:/^[\p{L}\p{N}\s\-_]+$/u'],
            'DiaChi'      => ['required', 'string', 'max:200'],
            'SoDienThoai' => ['required', 'regex:/^\d+$/', 'digits_between:10,11', 'starts_with:0'],
            'Email'       => ['nullable', 'email', 'max:150'],
            'GioMoCua'    => ['required', 'date_format:H:i'],
            'GioDongCua'  => ['required', 'date_format:H:i'],
            'latitude'    => ['nullable', 'numeric', 'between:-90,90'],
            'longitude'   => ['nullable', 'numeric', 'between:-180,180'],
            'phi_ship_co_ban' => ['nullable', 'numeric', 'min:0'],
            'phi_ship_moi_km' => ['nullable', 'numeric', 'min:0'],
            'km_mien_phi' => ['nullable', 'numeric', 'min:0'],
            'HinhAnh'     => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ], [
            'TenNhaHang.required' => 'Tên nhà hàng không được bỏ trống',
            'TenNhaHang.max'      => 'Tên nhà hàng không quá 100 ký tự',
            'TenNhaHang.regex'    => 'Tên nhà hàng không chứa ký tự đặc biệt',
            'DiaChi.required'     => 'Địa chỉ không được bỏ trống',
            'DiaChi.max'          => 'Địa chỉ không quá 200 ký tự',
            'SoDienThoai.required' => 'Số điện thoại không được bỏ trống',
            'SoDienThoai.regex'   => 'Số điện thoại chỉ chấp nhận chữ số',
            'SoDienThoai.digits_between' => 'Số điện thoại phải có 10–11 chữ số',
            'SoDienThoai.starts_with' => 'Số điện thoại phải bắt đầu bằng số 0',
            'GioMoCua.required'   => 'Giờ mở cửa không được bỏ trống',
            'GioMoCua.date_format' => 'Giờ mở cửa không hợp lệ',
            'GioDongCua.required' => 'Giờ đóng cửa không được bỏ trống',
            'GioDongCua.date_format' => 'Giờ đóng cửa không hợp lệ',
            'latitude.numeric'    => 'Vĩ độ phải là số',
            'longitude.numeric'   => 'Kinh độ phải là số',
            'phi_ship_co_ban.numeric' => 'Phí ship cơ bản phải là số',
            'phi_ship_moi_km.numeric' => 'Phí ship mỗi km phải là số',
            'km_mien_phi.numeric' => 'Khoảng cách miễn phí phải là số',
            'HinhAnh.image'       => 'File tải lên phải là hình ảnh',
            'HinhAnh.mimes'       => 'Hình ảnh phải có định dạng jpeg, png, jpg, hoặc gif',
            'HinhAnh.max'         => 'Kích thước hình ảnh không được vượt quá 2MB',
        ]);

        if ($request->hasFile('HinhAnh')) {
            // Delete old restaurant image if it exists and is local
            if ($restaurant->HinhAnh && !filter_var($restaurant->HinhAnh, FILTER_VALIDATE_URL)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($restaurant->HinhAnh);
            }
            $uploadedFileUrl = \CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary::uploadApi()->upload(
                $request->file('HinhAnh')->getRealPath(),
                [
                    'folder' => 'ta-food/restaurants',
                    'resource_type' => 'auto'
                ]
            )['secure_url'];
            $restaurant->HinhAnh = $uploadedFileUrl;
        }

        $restaurant->TenNhaHang  = $validated['TenNhaHang'];
        $restaurant->DiaChi      = $validated['DiaChi'];
        $restaurant->SoDienThoai = $validated['SoDienThoai'] ?? null;
        $restaurant->Email       = $validated['Email'] ?? null;
        
        // Đảm bảo định dạng HH:mm:00 để DB nhận diện đúng kiểu TIME
        $restaurant->GioMoCua    = $validated['GioMoCua'] . ':00';
        $restaurant->GioDongCua  = $validated['GioDongCua'] . ':00';

        // Lưu thông số định vị và giao hàng
        $restaurant->latitude        = $validated['latitude'] ?? 21.081827;
        $restaurant->longitude       = $validated['longitude'] ?? 105.842790;
        $restaurant->phi_ship_co_ban = $validated['phi_ship_co_ban'] ?? 15000;
        $restaurant->phi_ship_moi_km = $validated['phi_ship_moi_km'] ?? 5000;
        $restaurant->km_mien_phi     = $validated['km_mien_phi'] ?? 2.0;

        $restaurant->save();

        if ($isNew) {
            // Notify all admins
            $admins = \App\Models\User::where('VaiTro', 'QuanTri')->get();
            foreach ($admins as $admin) {
                \App\Services\NotificationService::add(
                    $admin->MaNguoiDung,
                    'Yêu cầu duyệt cửa hàng mới',
                    'Cửa hàng ' . $restaurant->TenNhaHang . ' vừa đăng ký và đang chờ phê duyệt.'
                );
            }
        }

        // Append custom attributes to serialized response array
        $restaurant->append('hinh_anh_url');

        return response()->json([
            'success' => true,
            'message' => $isNew ? 'Khởi tạo thông tin nhà hàng thành công!' : 'Cập nhật thông tin nhà hàng thành công!',
            'data' => $restaurant
        ]);
    }

    /**
     * Resolve Google Maps short URLs (maps.app.goo.gl)
     */
    public function resolveMapUrl(Request $request)
    {
        $request->validate([
            'url' => ['required', 'url']
        ]);

        $url = $request->input('url');

        try {
            // Follow redirects using Laravel's Http client
            $response = \Illuminate\Support\Facades\Http::withOptions([
                'allow_redirects' => [
                    'max' => 8,
                    'protocols' => ['http', 'https']
                ],
                'connect_timeout' => 5,
                'timeout' => 8
            ])->get($url);

            $finalUrl = $response->effectiveUri();

            return response()->json([
                'success' => true,
                'final_url' => (string)$finalUrl
            ]);
        } catch (\Exception $e) {
            // Fallback to PHP get_headers
            try {
                $headers = @get_headers($url, 1);
                if ($headers && isset($headers['Location'])) {
                    $location = is_array($headers['Location']) ? end($headers['Location']) : $headers['Location'];
                    return response()->json([
                        'success' => true,
                        'final_url' => $location
                    ]);
                }
            } catch (\Exception $ex) {}

            return response()->json([
                'success' => false,
                'message' => 'Không thể giải mã liên kết rút gọn này: ' . $e->getMessage()
            ], 422);
        }
    }
}
