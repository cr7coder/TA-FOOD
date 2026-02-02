<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GioHang;
use App\Models\GioHangChiTiet;
use App\Models\MonAn;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CartController extends Controller
{
    // Helper để lấy giỏ hàng (guest hoặc user)
    private function resolveCart(): GioHang
    {
        $user = Auth::user();
        
        if ($user) {
            return GioHang::firstOrCreate(['MaNguoiDung' => $user->MaNguoiDung]);
        }
        
        // Ensure session is started
        if (!session()->isStarted()) {
            session()->start();
        }
        
        $sessionId = session()->getId();
        \Log::info('Cart Session ID: ' . $sessionId); // DEBUG
        return GioHang::firstOrCreate(['session_id' => $sessionId]);
    }

    // GET /api/v1/cart
    public function index()
    {
        $cart = $this->resolveCart();
        $items = $cart->chiTiet()->with('monAn.nhaHang')->get();
        
        $total = $items->sum(function ($item) {
            return $item->monAn->Gia * $item->SoLuong;
        });

        return response()->json([
            'success' => true,
            'data' => [
                'items' => $items,
                'total' => $total,
                'count' => $items->count()
            ]
        ]);
    }

    // POST /api/v1/cart
    public function store(Request $request)
    {
        $request->validate([
            'MaMonAn' => 'required|exists:mon_an,MaMonAn',
            'SoLuong' => 'integer|min:1'
        ]);

        $food = MonAn::findOrFail($request->MaMonAn);
        
        if ($food->TrangThai !== 'Còn bán') {
            return response()->json([
                'success' => false,
                'message' => 'Món ăn này hiện không còn bán.'
            ], 400);
        }

        $cart = $this->resolveCart();
        $quantity = $request->input('SoLuong', 1);

        $cartItem = GioHangChiTiet::where('MaGioHang', $cart->MaGioHang)
            ->where('MaMonAn', $request->MaMonAn)
            ->first();

        if ($cartItem) {
            $cartItem->SoLuong += $quantity;
            $cartItem->save();
        } else {
            $cartItem = GioHangChiTiet::create([
                'MaGioHang' => $cart->MaGioHang,
                'MaMonAn' => $request->MaMonAn,
                'SoLuong' => $quantity
            ]);
        }

        // Reload cart để trả về format đúng
        $items = $cart->chiTiet()->with('monAn.nhaHang')->get();
        $total = $items->sum(function ($item) {
            return $item->monAn->Gia * $item->SoLuong;
        });

        return response()->json([
            'success' => true,
            'message' => 'Đã thêm món vào giỏ hàng!',
            'data' => [
                'items' => $items,
                'total' => $total,
                'count' => $items->count()
            ]
        ]);
    }

    // PUT /api/v1/cart/{id}
    public function update(Request $request, $id)
    {
        $request->validate([
            'SoLuong' => 'required|integer|min:0'
        ]);

        $cart = $this->resolveCart();
        $cartItem = GioHangChiTiet::where('MaChiTiet', $id)
            ->where('MaGioHang', $cart->MaGioHang)
            ->firstOrFail();

        if ($request->SoLuong == 0) {
            $cartItem->delete();
        } else {
            $cartItem->SoLuong = $request->SoLuong;
            $cartItem->save();
        }

        // Reload cart
        $items = $cart->chiTiet()->with('monAn.nhaHang')->get();
        $total = $items->sum(function ($item) {
            return $item->monAn->Gia * $item->SoLuong;
        });

        return response()->json([
            'success' => true,
            'message' => $request->SoLuong == 0 ? 'Đã xóa món khỏi giỏ hàng!' : 'Đã cập nhật số lượng!',
            'data' => [
                'items' => $items,
                'total' => $total,
                'count' => $items->count()
            ]
        ]);
    }

    // DELETE /api/v1/cart/{id}
    public function destroy($id)
    {
        $cart = $this->resolveCart();
        $cartItem = GioHangChiTiet::where('MaChiTiet', $id)
            ->where('MaGioHang', $cart->MaGioHang)
            ->firstOrFail();

        $cartItem->delete();

        // Reload cart
        $items = $cart->chiTiet()->with('monAn.nhaHang')->get();
        $total = $items->sum(function ($item) {
            return $item->monAn->Gia * $item->SoLuong;
        });

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa món khỏi giỏ hàng!',
            'data' => [
                'items' => $items,
                'total' => $total,
                'count' => $items->count()
            ]
        ]);
    }

    // DELETE /api/v1/cart
    public function clear()
    {
        $cart = $this->resolveCart();
        $cart->chiTiet()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa toàn bộ giỏ hàng!'
        ]);
    }
}
