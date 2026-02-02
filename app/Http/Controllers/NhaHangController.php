<?php

namespace App\Http\Controllers;

use App\Models\NhaHang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NhaHangController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index() {}

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $restaurant = NhaHang::with(['monAn' => function ($q) {
            $q->where('TrangThai', 'Còn bán');
        }])->where('MaNhaHang', $id)->firstOrFail();

        $foods = $restaurant->monAn;

        return view('restaurants.show', compact('restaurant', 'foods'));
    }
    public function editOwn(Request $request)
    {
        $sellerId = Auth::user()->MaNguoiDung;

        $restaurant = NhaHang::where('MaNguoiDung', $sellerId)->first();

        if (!$restaurant) {
            // Có thể chọn tạo mới thay vì báo lỗi tùy business
            return redirect()
                ->route('seller.foods.index')
                ->with('error', 'Tài khoản người bán chưa được gán nhà hàng.');
        }

        return view('seller.restaurant.edit', compact('restaurant'));
    }

    /**
     * Seller: Cập nhật thông tin nhà hàng của chính chủ
     */
    public function updateOwn(Request $request)
    {
        $sellerId = Auth::user()->MaNguoiDung;
        $restaurant = NhaHang::where('MaNguoiDung', $sellerId)->first();

        if (!$restaurant) {
            return redirect()->route('seller.foods.index')->with('error', 'Tài khoản người bán chưa được gán nhà hàng.');
        }

        // Validate: nhận HH:mm
        $validated = $request->validate([
            'TenNhaHang'  => ['required', 'string', 'max:150'],
            'DiaChi'      => ['required', 'string', 'max:255'],
            'SoDienThoai' => ['nullable', 'regex:/^\d{10,11}$/'],
            'GioMoCua'    => ['required', 'date_format:H:i'],
            'GioDongCua'  => ['required', 'date_format:H:i'],
        ], [
            'TenNhaHang.required' => 'Tên nhà hàng không được bỏ trống',
            'DiaChi.required'     => 'Địa chỉ không được bỏ trống',
            'SoDienThoai.regex'   => 'Số điện thoại phải là 10–11 chữ số',
            'GioMoCua.required'   => 'Giờ mở cửa không được bỏ trống',
            'GioMoCua.date_format' => 'Giờ mở cửa không hợp lệ',
            'GioDongCua.required' => 'Giờ đóngcửa không được bỏ trống',
            'GioDongCua.date_format' => 'Giờ đóngcửa không hợp lệ',
        ]);

        // Gán từng field để tránh lỗi fillable (hoặc dùng $restaurant->fill($validated) nếu đã set $fillable)
        $restaurant->TenNhaHang  = $validated['TenNhaHang'];
        $restaurant->DiaChi      = $validated['DiaChi'];
        $restaurant->SoDienThoai = $validated['SoDienThoai'] ?? null;
        $restaurant->GioMoCua    = $validated['GioMoCua'];    // mutator sẽ thêm :00
        $restaurant->GioDongCua  = $validated['GioDongCua'];  // mutator sẽ thêm :00

        // Kiểm tra thay đổi có tính cả 2 trường giờ
        $isDirty = $restaurant->isDirty(['TenNhaHang', 'DiaChi', 'SoDienThoai', 'GioMoCua', 'GioDongCua']);
        // if (!$isDirty) {
        //     return back()->withInput()->with('warning', 'Không có thông tin nào được cập nhật (3E.14)');
        // }

        $restaurant->save();

        // Trả về JSON nếu request AJAX
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Cập nhật thông tin nhà hàng thành công']);
        }

        return redirect()->route('seller.restaurant.edit')->with('success', 'Cập nhật thông tin nhà hàng thành công');
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(NhaHang $nhaHang)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, NhaHang $nhaHang)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(NhaHang $nhaHang)
    {
        //
    }
}
