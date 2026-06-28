<?php

namespace App\Http\Controllers\Api\Seller;

use App\Http\Controllers\Controller;
use App\Models\DonHang;
use App\Models\DonHangChiTiet;
use App\Models\MonAn;
use App\Models\NhaHang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Get Seller Dashboard Statistics
     */
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Security check for seller role
        if (!$user->isSeller()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access. Only sellers can view this dashboard.'
            ], 403);
        }

        $restaurant = NhaHang::where('MaNguoiDung', $user->MaNguoiDung)->first();

        if (!$restaurant) {
            return response()->json([
                'success' => false,
                'message' => 'No restaurant associated with this seller account.'
            ], 404);
        }

        $maNhaHang = $restaurant->MaNhaHang;
        $now = \Carbon\Carbon::now('Asia/Ho_Chi_Minh');
        
        $todayStart = $now->copy()->startOfDay();
        $todayEnd = $now->copy()->endOfDay();
        $yesterdayStart = $now->copy()->subDay()->startOfDay();
        $yesterdayEnd = $now->copy()->subDay()->endOfDay();
        $thisMonthStart = $now->copy()->startOfMonth();

        $calcGrowth = function ($current, $previous) {
            if ($previous == 0) return $current > 0 ? 100 : 0;
            return round((($current - $previous) / $previous) * 100, 1);
        };

        // 1. Revenue (Today vs Yesterday, only Completed orders)
        $getRevenue = function ($start, $end) use ($maNhaHang) {
            return (float) DonHangChiTiet::whereHas('monAn', function ($q) use ($maNhaHang) {
                $q->where('MaNhaHang', $maNhaHang);
            })->whereHas('donHang', function ($q) use ($start, $end) {
                $q->whereBetween('updated_at', [$start, $end])->where('TrangThai', 'Hoàn thành');
            })->sum(DB::raw('SoLuong * Gia'));
        };
        $revenueToday = $getRevenue($todayStart, $todayEnd);
        $revenueYesterday = $getRevenue($yesterdayStart, $yesterdayEnd);

        // 2. Orders (Today vs Yesterday, exclude Cancelled)
        $getOrders = function ($start, $end) use ($maNhaHang) {
            return DonHang::whereHas('chiTiet.monAn', function ($q) use ($maNhaHang) {
                $q->where('MaNhaHang', $maNhaHang);
            })->whereBetween('created_at', [$start, $end])->where('TrangThai', '!=', 'Hủy')->count();
        };
        $ordersToday = $getOrders($todayStart, $todayEnd);
        $ordersYesterday = $getOrders($yesterdayStart, $yesterdayEnd);

        // 3. Pending Orders (Currently active pending orders)
        $pendingOrders = DonHang::whereHas('chiTiet.monAn', function ($q) use ($maNhaHang) {
            $q->where('MaNhaHang', $maNhaHang);
        })->where('TrangThai', 'Chờ xử lý')->count();

        // 4. Customers (Today vs Yesterday)
        $getCustomers = function ($start, $end) use ($maNhaHang) {
            return DonHang::whereHas('chiTiet.monAn', function ($q) use ($maNhaHang) {
                $q->where('MaNhaHang', $maNhaHang);
            })->whereBetween('created_at', [$start, $end])->distinct('MaNguoiDung')->count('MaNguoiDung');
        };
        $customersToday = $getCustomers($todayStart, $todayEnd);
        $customersYesterday = $getCustomers($yesterdayStart, $yesterdayEnd);

        // 5. Recent Orders (Top 5, prioritize pending)
        $recentOrders = DonHang::whereHas('chiTiet.monAn', function ($q) use ($maNhaHang) {
                $q->where('MaNhaHang', $maNhaHang);
            })
            ->with(['chiTiet' => function ($q) use ($maNhaHang) {
                $q->whereHas('monAn', function ($q2) use ($maNhaHang) {
                    $q2->where('MaNhaHang', $maNhaHang);
                });
            }])
            ->orderByRaw("CASE WHEN TrangThai = 'Chờ xử lý' THEN 1 ELSE 2 END")
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($order) {
                $uiStatusMap = [
                    'Chờ xử lý'     => 'Chờ xác nhận',
                    'Đã xác nhận'   => 'Đã xác nhận',
                    'Đang chuẩn bị' => 'Đang chuẩn bị',
                    'Đang giao'     => 'Đang giao',
                    'Hoàn thành'    => 'Hoàn thành',
                    'Hủy'           => 'Đã hủy'
                ];
                $displayStatus = $uiStatusMap[$order->TrangThai] ?? $order->TrangThai;

                return [
                    'id' => $order->MaDonHang,
                    'customer_name' => $order->TenKhachHang ?: ($order->nguoiDung->HoTen ?? 'Khách lẻ'),
                    'amount' => (float)$order->chiTiet->sum(fn($ct) => $ct->SoLuong * $ct->Gia),
                    'status' => $displayStatus,
                    'created_at' => $order->created_at->format('H:i d/m/Y'),
                ];
            });

        // 6. Top Selling Foods (This Month)
        $topSellingFoods = DonHangChiTiet::whereHas('monAn', function ($q) use ($maNhaHang) {
                $q->where('MaNhaHang', $maNhaHang);
            })
            ->whereHas('donHang', function ($q) use ($thisMonthStart, $now) {
                $q->where('TrangThai', 'Hoàn thành')
                  ->whereBetween('updated_at', [$thisMonthStart, $now]);
            })
            ->select('MaMonAn', DB::raw('SUM(SoLuong) as total_sold'), DB::raw('SUM(SoLuong * Gia) as total_revenue'))
            ->with('monAn')
            ->groupBy('MaMonAn')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                return [
                    'food_id' => $item->MaMonAn,
                    'food_name' => $item->monAn->TenMonAn ?? 'Unknown',
                    'sold_count' => (int)$item->total_sold,
                    'revenue' => (float)$item->total_revenue,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'restaurant' => [
                    'name' => $restaurant->TenNhaHang,
                    'address' => $restaurant->DiaChi,
                ],
                'stats' => [
                    'revenue' => [
                        'value' => $revenueToday,
                        'growth' => $calcGrowth($revenueToday, $revenueYesterday)
                    ],
                    'orders' => [
                        'value' => $ordersToday,
                        'growth' => $calcGrowth($ordersToday, $ordersYesterday)
                    ],
                    'customers' => [
                        'value' => $customersToday,
                        'growth' => $calcGrowth($customersToday, $customersYesterday)
                    ],
                    'pending_orders' => $pendingOrders
                ],
                'recent_orders' => $recentOrders,
                'top_selling_foods' => $topSellingFoods,
            ]
        ]);
    }
}
