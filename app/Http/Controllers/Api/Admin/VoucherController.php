<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\GiamGia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Validation\Rule;

class VoucherController extends Controller
{
    /**
     * Check if user is admin
     */
    private function checkAdmin()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user || !$user->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access. Only admins can access this resource.'
            ], 403);
        }
        return null;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($response = $this->checkAdmin()) return $response;

        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');
        $status = $request->input('status');
        $percentFrom = $request->input('percent_from');
        $percentTo = $request->input('percent_to');

        $query = GiamGia::orderBy('created_at', 'desc');

        if ($search) {
            $query->where('MaCode', 'like', '%' . $search . '%');
        }

        if ($status === 'active') {
            $query->active();
        } elseif ($status === 'expired') {
            $query->where('NgayKetThuc', '<', Carbon::now());
        } elseif ($status === 'upcoming') {
            $query->where('NgayBatDau', '>', Carbon::now());
        }

        if ($percentFrom !== null) {
            $query->where('PhanTram', '>=', $percentFrom);
        }

        if ($percentTo !== null) {
            $query->where('PhanTram', '<=', $percentTo);
        }

        $vouchers = $query->paginate($perPage);

        // Calculate stats
        $total = GiamGia::count();
        $active = GiamGia::active()->count();
        $expired = GiamGia::where('NgayKetThuc', '<', Carbon::now())->count();
        $expiringSoon = GiamGia::expiringSoon()->count();

        return response()->json([
            'success' => true,
            'data' => [
                'items' => $vouchers->items(),
                'pagination' => [
                    'current_page' => $vouchers->currentPage(),
                    'last_page' => $vouchers->lastPage(),
                    'per_page' => $vouchers->perPage(),
                    'total' => $vouchers->total(),
                ],
                'stats' => [
                    'total' => $total,
                    'active' => $active,
                    'expired' => $expired,
                    'expiring_soon' => $expiringSoon
                ]
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if ($response = $this->checkAdmin()) return $response;

        try {
            $rules = [
                'MaCode' => [
                    'required',
                    'string',
                    'max:50',
                    Rule::unique('giam_gia', 'MaCode')->whereNull('deleted_at')
                ],
                'LoaiGiamGia' => 'required|string|in:PhanTram,TienMat',
                'PhanTram' => 'required_if:LoaiGiamGia,PhanTram|numeric|min:0|max:100',
                'GiamToiDa' => 'required_if:LoaiGiamGia,TienMat|nullable|numeric|min:0|max:99999999',
                'DonHangToiThieu' => 'nullable|numeric|min:0|max:99999999',
                'NgayBatDau' => 'required|date',
                'NgayKetThuc' => 'required|date|after_or_equal:NgayBatDau',
                'SoLuongToiDa' => 'nullable|integer|min:1',
                'GioiHanNguoiDung' => 'nullable|integer|min:1',
                'MoTa' => 'nullable|string'
            ];

            $messages = [
                'MaCode.required'   => 'Mã giảm giá không được bỏ trống',
                'MaCode.max'        => 'Mã giảm giá không quá 50 ký tự',
                'MaCode.unique'     => 'Mã giảm giá đã tồn tại',
                'LoaiGiamGia.required' => 'Loại giảm giá không được bỏ trống',
                'PhanTram.required_if' => 'Phần trăm giảm giá không được bỏ trống',
                'PhanTram.numeric'     => 'Phần trăm giảm giá phải là số',
                'PhanTram.min'         => 'Phần trăm giảm giá phải lớn hơn hoặc bằng 0',
                'PhanTram.max'         => 'Phần trăm giảm giá tối đa là 100',
                'GiamToiDa.required_if' => 'Số tiền giảm không được bỏ trống',
                'GiamToiDa.numeric'     => 'Số tiền giảm phải là số',
                'GiamToiDa.min'         => 'Số tiền giảm phải lớn hơn hoặc bằng 0',
                'DonHangToiThieu.numeric' => 'Đơn hàng tối thiểu phải là số',
                'DonHangToiThieu.min'     => 'Đơn hàng tối thiểu phải lớn hơn hoặc bằng 0',
                'NgayBatDau.required'   => 'Ngày bắt đầu không được bỏ trống',
                'NgayBatDau.date'       => 'Ngày bắt đầu không hợp lệ',
                'NgayKetThuc.required'  => 'Ngày kết thúc không được bỏ trống',
                'NgayKetThuc.date'      => 'Ngày kết thúc không hợp lệ',
                'NgayKetThuc.after_or_equal' => 'Ngày kết thúc phải sau hoặc bằng ngày bắt đầu',
                'SoLuongToiDa.integer'  => 'Tổng số lượng phát hành phải là số nguyên',
                'SoLuongToiDa.min'      => 'Tổng số lượng phát hành phải lớn hơn hoặc bằng 1',
                'GioiHanNguoiDung.integer' => 'Giới hạn sử dụng/1 User phải là số nguyên',
                'GioiHanNguoiDung.min'     => 'Giới hạn sử dụng/1 User phải lớn hơn hoặc bằng 1',
            ];

            $validated = $request->validate($rules, $messages);

            // Chuẩn hóa dữ liệu theo loại giảm giá
            if ($validated['LoaiGiamGia'] === 'TienMat') {
                $validated['PhanTram'] = 0;
            }

            // Nếu để trống đơn hàng tối thiểu, mặc định là 0 (không giới hạn)
            if (empty($validated['DonHangToiThieu'])) {
                $validated['DonHangToiThieu'] = 0;
            }

            $voucher = GiamGia::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Tạo voucher thành công!',
                'data' => $voucher
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        if ($response = $this->checkAdmin()) return $response;

        $voucher = GiamGia::find($id);

        if (!$voucher) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy voucher.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $voucher
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        if ($response = $this->checkAdmin()) return $response;

        $voucher = GiamGia::find($id);

        if (!$voucher) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy voucher.'
            ], 404);
        }

        try {
            $rules = [
                'MaCode' => [
                    'required',
                    'string',
                    'max:50',
                    Rule::unique('giam_gia', 'MaCode')->ignore($id, 'MaGiamGia')->whereNull('deleted_at')
                ],
                'LoaiGiamGia' => 'required|string|in:PhanTram,TienMat',
                'PhanTram' => 'required_if:LoaiGiamGia,PhanTram|numeric|min:0|max:100',
                'GiamToiDa' => 'required_if:LoaiGiamGia,TienMat|nullable|numeric|min:0|max:99999999',
                'DonHangToiThieu' => 'nullable|numeric|min:0|max:99999999',
                'NgayBatDau' => 'required|date',
                'NgayKetThuc' => 'required|date|after_or_equal:NgayBatDau',
                'SoLuongToiDa' => 'nullable|integer|min:1',
                'GioiHanNguoiDung' => 'nullable|integer|min:1',
                'MoTa' => 'nullable|string'
            ];

            $messages = [
                'MaCode.required'   => 'Mã giảm giá không được bỏ trống',
                'MaCode.max'        => 'Mã giảm giá không quá 50 ký tự',
                'MaCode.unique'     => 'Mã giảm giá đã tồn tại',
                'LoaiGiamGia.required' => 'Loại giảm giá không được bỏ trống',
                'PhanTram.required_if' => 'Phần trăm giảm giá không được bỏ trống',
                'PhanTram.numeric'     => 'Phần trăm giảm giá phải là số',
                'PhanTram.min'         => 'Phần trăm giảm giá phải lớn hơn hoặc bằng 0',
                'PhanTram.max'         => 'Phần trăm giảm giá tối đa là 100',
                'GiamToiDa.required_if' => 'Số tiền giảm không được bỏ trống',
                'GiamToiDa.numeric'     => 'Số tiền giảm phải là số',
                'GiamToiDa.min'         => 'Số tiền giảm phải lớn hơn hoặc bằng 0',
                'DonHangToiThieu.numeric' => 'Đơn hàng tối thiểu phải là số',
                'DonHangToiThieu.min'     => 'Đơn hàng tối thiểu phải lớn hơn hoặc bằng 0',
                'NgayBatDau.required'   => 'Ngày bắt đầu không được bỏ trống',
                'NgayBatDau.date'       => 'Ngày bắt đầu không hợp lệ',
                'NgayKetThuc.required'  => 'Ngày kết thúc không được bỏ trống',
                'NgayKetThuc.date'      => 'Ngày kết thúc không hợp lệ',
                'NgayKetThuc.after_or_equal' => 'Ngày kết thúc phải sau hoặc bằng ngày bắt đầu',
                'SoLuongToiDa.integer'  => 'Tổng số lượng phát hành phải là số nguyên',
                'SoLuongToiDa.min'      => 'Tổng số lượng phát hành phải lớn hơn hoặc bằng 1',
                'GioiHanNguoiDung.integer' => 'Giới hạn sử dụng/1 User phải là số nguyên',
                'GioiHanNguoiDung.min'     => 'Giới hạn sử dụng/1 User phải lớn hơn hoặc bằng 1',
            ];

            $validated = $request->validate($rules, $messages);

            // Chuẩn hóa dữ liệu theo loại giảm giá
            if ($validated['LoaiGiamGia'] === 'TienMat') {
                $validated['PhanTram'] = 0;
            }

            // Nếu để trống đơn hàng tối thiểu, mặc định là 0 (không giới hạn)
            if (empty($validated['DonHangToiThieu'])) {
                $validated['DonHangToiThieu'] = 0;
            }

            $voucher->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật voucher thành công!',
                'data' => $voucher
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        if ($response = $this->checkAdmin()) return $response;

        $voucher = GiamGia::find($id);

        if (!$voucher) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy voucher.'
            ], 404);
        }

        try {
            $voucher->delete();

            return response()->json([
                'success' => true,
                'message' => 'Xóa voucher thành công!'
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
