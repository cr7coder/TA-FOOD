<?php

namespace App\Http\Controllers;

use App\Models\GioHang;
use App\Models\GioHangChiTiet;
use App\Models\MonAn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GioHangController extends Controller
{
    private function resolveCart(): GioHang
    {
        $user = Auth::user();
        if ($user) {
            return GioHang::firstOrCreate(['MaNguoiDung' => $user->MaNguoiDung]);
        }
        $sessionId = session()->getId();
        return GioHang::firstOrCreate(['session_id' => $sessionId]);
    }

    private function formatCartResponse(GioHang $gioHang)
    {
        $gioHang->load('chiTiet.monAn');

        $items = $gioHang->chiTiet->map(function ($ct) {
            return [
                'id'       => $ct->MaMonAn,
                'name'     => $ct->monAn->TenMonAn ?? 'Món đã xoá',
                'price'    => (int)($ct->monAn->Gia ?? 0),
                'image'    => ($ct->monAn && $ct->monAn->HinhAnh)
                    ? asset('images/' . $ct->monAn->HinhAnh)
                    : asset('images/no-image.png'),
                'quantity' => (int)$ct->SoLuong,
                'total'    => (int)$ct->ThanhTien,
            ];
        })->values();

        return response()->json([
            'success' => true,
            'data'    => $items,
            'count'   => (int)$gioHang->TongSoLuong,
            'total'   => (int)$gioHang->TongTien,
        ]);
    }

    public function index()
    {
        $gioHang = $this->resolveCart();
        return $this->formatCartResponse($gioHang);
    }

    public function store(Request $request)
    {
        $request->validate([
            'MaMonAn' => 'required|integer|exists:mon_an,MaMonAn',
            'SoLuong' => 'nullable|integer|min:1',
        ]);

        $monAn = MonAn::find($request->MaMonAn);
        if (!$monAn || $monAn->TrangThai !== 'Còn bán') {
            return response()->json(['success' => false, 'message' => 'Món ăn không khả dụng'], 422);
        }

        $gioHang = $this->resolveCart();

        $chiTiet = GioHangChiTiet::firstOrNew([
            'MaGioHang' => $gioHang->MaGioHang,
            'MaMonAn'   => $monAn->MaMonAn,
        ]);

        $added_new = !$chiTiet->exists; // TRUE nếu là thêm mới, FALSE nếu đã có

        $chiTiet->SoLuong = ($chiTiet->exists ? $chiTiet->SoLuong : 0) + ($request->SoLuong ?? 1);
        $chiTiet->save();

        // Lấy response data, thêm trường phân biệt
        $res = $this->formatCartResponse($gioHang);
        $data = $res->getData(true);
        $data['added_new'] = $added_new;

        return response()->json($data);
    }

    public function update(Request $request, $maMonAn)
    {
        $request->validate(['SoLuong' => 'required|integer|min:0']);

        $gioHang = $this->resolveCart();

        $ct = GioHangChiTiet::where('MaGioHang', $gioHang->MaGioHang)
            ->where('MaMonAn', $maMonAn)
            ->first();

        if (!$ct) {
            return $this->formatCartResponse($gioHang);
        }

        if ($request->SoLuong == 0) {
            $ct->delete();
        } else {
            $ct->SoLuong = $request->SoLuong;
            $ct->save();
        }

        return $this->formatCartResponse($gioHang);
    }

    public function destroy($maMonAn)
    {
        $gioHang = $this->resolveCart();

        GioHangChiTiet::where('MaGioHang', $gioHang->MaGioHang)
            ->where('MaMonAn', $maMonAn)
            ->delete();

        return $this->formatCartResponse($gioHang);
    }

    public function clear()
    {
        $gioHang = $this->resolveCart();
        GioHangChiTiet::where('MaGioHang', $gioHang->MaGioHang)->delete();

        return $this->formatCartResponse($gioHang);
    }
}
