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
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user->isSeller()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $restaurant = NhaHang::where('MaNguoiDung', $user->MaNguoiDung)->first();
        if (!$restaurant) {
            return response()->json(['success' => false, 'message' => 'Restaurant not found'], 404);
        }

        $maNhaHang = $restaurant->MaNhaHang;
        
        $timeRange = $request->input('time_range', 'this_month');
        $now = Carbon::now('Asia/Ho_Chi_Minh');

        if ($timeRange === 'last_month') {
            $currentStart = $now->copy()->subMonth()->startOfMonth();
            $currentEnd = $now->copy()->subMonth()->endOfMonth();
            $prevStart = $now->copy()->subMonths(2)->startOfMonth();
            $prevEnd = $now->copy()->subMonths(2)->endOfMonth();
        } elseif ($timeRange === 'this_quarter') {
            $currentStart = $now->copy()->startOfQuarter();
            $currentEnd = $now->copy();
            $prevStart = $now->copy()->subQuarter()->startOfQuarter();
            $prevEnd = $now->copy()->subQuarter()->endOfQuarter();
        } elseif ($timeRange === 'this_year') {
            $currentStart = $now->copy()->startOfYear();
            $currentEnd = $now->copy();
            $prevStart = $now->copy()->subYear()->startOfYear();
            $prevEnd = $now->copy()->subYear()->endOfYear();
        } elseif ($timeRange === 'all_time') {
            $currentStart = $now->copy()->subYears(20);
            $currentEnd = $now->copy();
            $prevStart = $now->copy()->subYears(40);
            $prevEnd = $now->copy()->subYears(20);
        } else {
            // this_month
            $currentStart = $now->copy()->startOfMonth();
            $currentEnd = $now->copy();
            $prevStart = $now->copy()->subMonth()->startOfMonth();
            $prevEnd = $now->copy()->subMonth()->endOfMonth();
        }

        // Helper to get stats for a range
        $getStats = function ($start, $end) use ($maNhaHang) {
            $query = DonHangChiTiet::whereHas('monAn', function ($q) use ($maNhaHang) {
                $q->where('MaNhaHang', $maNhaHang);
            })->whereHas('donHang', function ($q) use ($start, $end) {
                $q->whereBetween('created_at', [$start, $end])
                  ->where('TrangThai', 'Hoàn thành');
            });

            $revenue = (float) $query->sum(DB::raw('SoLuong * Gia'));
            
            // Count unique orders in this range that contain items from this restaurant
            $ordersCount = DonHang::whereHas('chiTiet.monAn', function ($q) use ($maNhaHang) {
                $q->where('MaNhaHang', $maNhaHang);
            })->whereBetween('created_at', [$start, $end])->count();

            // Completed orders count for AOV
            $completedOrdersCount = DonHang::whereHas('chiTiet.monAn', function ($q) use ($maNhaHang) {
                $q->where('MaNhaHang', $maNhaHang);
            })->where('TrangThai', 'Hoàn thành')
              ->whereBetween('created_at', [$start, $end])->count();

            $aov = $completedOrdersCount > 0 ? $revenue / $completedOrdersCount : 0;

            // Calculate actual payout after commission
            $orders = DonHang::whereHas('chiTiet.monAn', function ($q) use ($maNhaHang) {
                $q->where('MaNhaHang', $maNhaHang);
            })->whereBetween('created_at', [$start, $end])
              ->where('TrangThai', 'Hoàn thành')
              ->with(['chiTiet.monAn.nhaHang', 'doiTacVanChuyen'])
              ->get();

            // Get commission rate from restaurant
            $restaurantModel = \App\Models\NhaHang::where('MaNhaHang', $maNhaHang)->first();
            $commissionRate = (float)($restaurantModel->commission_rate ?? 15.00);

            $payout = 0.0;
            $commissionTotal = 0.0;
            foreach ($orders as $order) {
                $tamTinh = $order->chiTiet->sum(fn($i) => (float)$i->Gia * $i->SoLuong);
                $phiVanChuyen = (float)$order->PhiVanChuyen;
                $carrierCost = $this->getOrderCarrierCost($order);
                $commissionAmount = round($tamTinh * ($commissionRate / 100), 0);
                $commissionTotal += $commissionAmount;
                $payout += ($tamTinh - $commissionAmount) + ($phiVanChuyen - $carrierCost);
            }

            return [
                'revenue' => $revenue,
                'orders' => $completedOrdersCount,
                'aov' => $aov,
                'payout' => $payout,
                'commission_rate' => $commissionRate,
                'commission_amount' => $commissionTotal
            ];
        };

        $currentStats = $getStats($currentStart, $currentEnd);
        $prevStats = $getStats($prevStart, $prevEnd);

        // Growth Calculation
        $calcGrowth = function ($current, $previous) {
            if ($previous == 0) return $current > 0 ? 100 : 0;
            return round((($current - $previous) / $previous) * 100, 1);
        };

        $summary = [
            'revenue' => [
                'value' => $currentStats['revenue'],
                'growth' => $calcGrowth($currentStats['revenue'], $prevStats['revenue'])
            ],
            'payout' => [
                'value' => $currentStats['payout'],
                'growth' => $calcGrowth($currentStats['payout'], $prevStats['payout']),
                'commission_rate' => $currentStats['commission_rate'],
                'commission_amount' => $currentStats['commission_amount']
            ],
            'orders' => [
                'value' => $currentStats['orders'],
                'growth' => $calcGrowth($currentStats['orders'], $prevStats['orders'])
            ],
            'aov' => [
                'value' => $currentStats['aov'],
                'growth' => $calcGrowth($currentStats['aov'], $prevStats['aov'])
            ]
        ];

        // --- Revenue Last 6 Months ---
        $monthlyRevenue = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = $now->copy()->subMonths($i);
            $start = $month->copy()->startOfMonth();
            $end = $month->copy()->endOfMonth();
            
            $rev = (float) DonHangChiTiet::whereHas('monAn', function ($q) use ($maNhaHang) {
                $q->where('MaNhaHang', $maNhaHang);
            })->whereHas('donHang', function ($q) use ($start, $end) {
                $q->whereBetween('created_at', [$start, $end])
                  ->where('TrangThai', 'Hoàn thành');
            })->sum(DB::raw('SoLuong * Gia'));

            // Mock order count for the figma "180 đơn" label
            $ord = DonHang::whereHas('chiTiet.monAn', function ($q) use ($maNhaHang) {
                $q->where('MaNhaHang', $maNhaHang);
            })->whereBetween('created_at', [$start, $end])->count();

            $monthlyRevenue[] = [
                'label' => 'T' . $month->format('n'),
                'revenue' => $rev,
                'orders' => $ord
            ];
        }

        // --- Top Selling Foods ---
        $topFoods = DonHangChiTiet::whereHas('monAn', function ($q) use ($maNhaHang) {
                $q->where('MaNhaHang', $maNhaHang);
            })
            ->whereHas('donHang', function ($q) use ($currentStart, $currentEnd) {
                $q->where('TrangThai', 'Hoàn thành')
                  ->whereBetween('created_at', [$currentStart, $currentEnd]);
            })
            ->select('MaMonAn', DB::raw('SUM(SoLuong) as total_sold'), DB::raw('SUM(SoLuong * Gia) as total_revenue'))
            ->with('monAn')
            ->groupBy('MaMonAn')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->monAn->TenMonAn ?? 'N/A',
                    'sold' => (int)$item->total_sold,
                    'revenue' => (float)$item->total_revenue
                ];
            });

        // --- Stats by Category (All system categories, grouping minor ones) ---
        $allCategories = MonAn::distinct()
            ->pluck('DanhMuc')
            ->filter()
            ->values();

        $categoryRevenueRaw = DonHangChiTiet::whereHas('monAn', function ($q) use ($maNhaHang) {
                $q->where('MaNhaHang', $maNhaHang);
            })
            ->join('mon_an', 'don_hang_chi_tiet.MaMonAn', '=', 'mon_an.MaMonAn')
            ->join('don_hang', 'don_hang.MaDonHang', '=', 'don_hang_chi_tiet.MaDonHang')
            ->where('don_hang.TrangThai', 'Hoàn thành')
            ->whereBetween('don_hang.created_at', [$currentStart, $currentEnd])
            ->select('mon_an.DanhMuc', DB::raw('SUM(don_hang_chi_tiet.SoLuong * don_hang_chi_tiet.Gia) as revenue'))
            ->groupBy('mon_an.DanhMuc')
            ->get()
            ->pluck('revenue', 'DanhMuc');

        $categoryStats = $allCategories->map(function ($cat) use ($categoryRevenueRaw) {
            return [
                'category' => $cat,
                'revenue' => (float)($categoryRevenueRaw[$cat] ?? 0)
            ];
        })->sortByDesc('revenue')->values();

        // If more than 5 categories, group the rest into "Khác"
        if ($categoryStats->count() > 5) {
            $top5 = $categoryStats->take(4);
            $others = $categoryStats->slice(4);
            $otherRevenue = $others->sum('revenue');
            
            $categoryStats = $top5->push([
                'category' => 'Khác',
                'revenue' => $otherRevenue
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'summary' => $summary,
                'monthly_revenue' => $monthlyRevenue,
                'top_foods' => $topFoods,
                'category_stats' => $categoryStats
            ]
        ]);
    }

    private function getOrderCarrierCost($order)
    {
        $phiVanChuyen = (float)$order->PhiVanChuyen;
        $firstDetail = $order->chiTiet->first();
        $restaurant = $firstDetail->monAn->nhaHang ?? null;
        
        $phiCoBan = $restaurant ? (float)($restaurant->phi_ship_co_ban ?? 15000) : 15000;
        $phiMoiKm = $restaurant ? (float)($restaurant->phi_ship_moi_km ?? 5000) : 5000;
        $kmMienPhi = $restaurant ? (float)($restaurant->km_mien_phi ?? 2.0) : 2.0;

        $distance = 0.0;
        if ($phiVanChuyen > 0) {
            if ($phiVanChuyen <= $phiCoBan) {
                $distance = $kmMienPhi;
            } else {
                $extraFee = $phiVanChuyen - $phiCoBan;
                $extraKm = $phiMoiKm > 0 ? ($extraFee / $phiMoiKm) : 0;
                $distance = round($kmMienPhi + $extraKm, 1);
            }
        } else {
            $distance = 1.2;
        }

        $carrierCost = 0;
        if ($order->doiTacVanChuyen) {
            $carrierBase = (float)$order->doiTacVanChuyen->phi_van_chuyen;
            $carrierKmPrice = (float)$order->doiTacVanChuyen->phi_km;
            $carrierCost = $carrierBase + ($distance * $carrierKmPrice);
        }
        
        return $carrierCost;
    }
}
