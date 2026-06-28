<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\DonHang;
use App\Models\DonHangChiTiet;
use App\Models\NhaHang;
use App\Models\MonAn;
use App\Models\User;
use App\Models\DanhMuc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    private function checkAdmin()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user || !$user->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Quyền truy cập bị từ chối.'
            ], 403);
        }
        return null;
    }

    public function index(Request $request)
    {
        if ($response = $this->checkAdmin()) return $response;

        $now = Carbon::now('Asia/Ho_Chi_Minh');
        $thisYear = $now->year;
        $thisMonth = $now->month;

        // Current and previous month boundaries
        $startOfThisMonth = $now->copy()->startOfMonth();
        $startOfLastMonth = $now->copy()->subMonth()->startOfMonth();
        $endOfLastMonth = $now->copy()->subMonth()->endOfMonth();

        // ── 1. KPI 1: TOTAL REVENUE & GROWTH (SUM OF SOLUONG * GIA) ──
        // Sum of all completed items in history
        $revenueTotal = (float) DonHangChiTiet::whereHas('donHang', function ($q) {
            $q->where('TrangThai', 'Hoàn thành');
        })->sum(DB::raw('SoLuong * Gia'));

        // Sum of completed items in this month vs last month
        $revenueThisMonth = (float) DonHangChiTiet::whereHas('donHang', function ($q) use ($startOfThisMonth, $now) {
            $q->where('TrangThai', 'Hoàn thành')->whereBetween('created_at', [$startOfThisMonth, $now]);
        })->sum(DB::raw('SoLuong * Gia'));

        $revenueLastMonth = (float) DonHangChiTiet::whereHas('donHang', function ($q) use ($startOfLastMonth, $endOfLastMonth) {
            $q->where('TrangThai', 'Hoàn thành')->whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth]);
        })->sum(DB::raw('SoLuong * Gia'));

        $revenueGrowth = $revenueLastMonth > 0 
            ? round((($revenueThisMonth - $revenueLastMonth) / $revenueLastMonth) * 100, 1) 
            : ($revenueThisMonth > 0 ? 100.0 : 0.0);

        // ── 2. KPI 2: TOTAL ORDERS & GROWTH ──
        // Total orders in history
        $ordersTotal = DonHang::count();

        // Total orders this month vs last month
        $ordersThisMonth = DonHang::whereBetween('created_at', [$startOfThisMonth, $now])->count();
        $ordersLastMonth = DonHang::whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])->count();
        $ordersGrowth = $ordersLastMonth > 0 
            ? round((($ordersThisMonth - $ordersLastMonth) / $ordersLastMonth) * 100, 1) 
            : ($ordersThisMonth > 0 ? 100.0 : 0.0);

        // ── 3. KPI 3: AVERAGE ORDER VALUE & GROWTH ──
        // Completed orders count
        $completedTotal = DonHang::where('TrangThai', 'Hoàn thành')->count();
        $aovTotal = $completedTotal > 0 ? round($revenueTotal / $completedTotal, 0) : 0;

        // AOV this month vs last month
        $completedThisMonth = DonHang::where('TrangThai', 'Hoàn thành')
            ->whereBetween('created_at', [$startOfThisMonth, $now])
            ->count();
        $completedLastMonth = DonHang::where('TrangThai', 'Hoàn thành')
            ->whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])
            ->count();

        $aovThisMonth = $completedThisMonth > 0 ? $revenueThisMonth / $completedThisMonth : 0;
        $aovLastMonth = $completedLastMonth > 0 ? $revenueLastMonth / $completedLastMonth : 0;
        $aovGrowth = $aovLastMonth > 0 
            ? round((($aovThisMonth - $aovLastMonth) / $aovLastMonth) * 100, 1) 
            : ($aovThisMonth > 0 ? 100.0 : 0.0);

        // ── 4. KPI 4: COMPLETION RATE & GROWTH ──
        $completionRateTotal = $ordersTotal > 0 ? round(($completedTotal / $ordersTotal) * 100, 1) : 0;

        $completionRateThisMonth = $ordersThisMonth > 0 ? ($completedThisMonth / $ordersThisMonth) * 100 : 0;
        $completionRateLastMonth = $ordersLastMonth > 0 ? ($completedLastMonth / $ordersLastMonth) * 100 : 0;
        $completionGrowth = $completionRateLastMonth > 0 
            ? round((($completionRateThisMonth - $completionRateLastMonth) / $completionRateLastMonth) * 100, 1) 
            : ($completionRateThisMonth > 0 ? 100.0 : 0.0);

        $kpi = [
            'revenue' => [
                'total' => $revenueTotal,
                'fmt' => number_format($revenueTotal, 0, ',', '.'),
                'change' => $revenueGrowth,
                'label' => 'So với tháng trước'
            ],
            'orders' => [
                'total' => $ordersTotal,
                'fmt' => number_format($ordersTotal, 0, ',', '.'),
                'change' => $ordersGrowth,
                'label' => 'So với tháng trước'
            ],
            'aov' => [
                'total' => $aovTotal,
                'fmt' => number_format($aovTotal, 0, ',', '.'),
                'change' => $aovGrowth,
                'label' => 'So với tháng trước'
            ],
            'completion_rate' => [
                'total' => $completionRateTotal,
                'fmt' => number_format($completionRateTotal, 1, ',', '.') . '%',
                'change' => $completionGrowth,
                'label' => 'Đơn thành công'
            ]
        ];

        // ── 5. MONTHLY TREND (T1 TO T12) ──
        $dbMonthlyData = DonHangChiTiet::join('don_hang', 'don_hang.MaDonHang', '=', 'don_hang_chi_tiet.MaDonHang')
            ->where('don_hang.TrangThai', 'Hoàn thành')
            ->whereYear('don_hang.created_at', $thisYear)
            ->select(
                DB::raw('MONTH(don_hang.created_at) as month'),
                DB::raw('SUM(don_hang_chi_tiet.SoLuong * don_hang_chi_tiet.Gia) as revenue')
            )
            ->groupBy(DB::raw('MONTH(don_hang.created_at)'))
            ->pluck('revenue', 'month');

        $dbMonthlyOrders = DonHang::select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as orders')
            )
            ->whereYear('created_at', $thisYear)
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->pluck('orders', 'month');

        $dbCompletedMonthlyOrders = DonHang::select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as orders')
            )
            ->where('TrangThai', 'Hoàn thành')
            ->whereYear('created_at', $thisYear)
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->pluck('orders', 'month');

        $monthlyData = [];
        $prevRevenue = null;

        for ($m = 1; $m <= 12; $m++) {
            $mRev = (float) ($dbMonthlyData[$m] ?? 0);
            $mOrd = (int) ($dbMonthlyOrders[$m] ?? 0);
            $mComp = (int) ($dbCompletedMonthlyOrders[$m] ?? 0);
            $mAov = $mComp > 0 ? round($mRev / $mComp, 0) : 0;

            $growth = '';
            if ($prevRevenue !== null && $prevRevenue > 0) {
                $growthVal = round((($mRev - $prevRevenue) / $prevRevenue) * 100, 1);
                $growth = ($growthVal >= 0 ? '+' : '') . $growthVal . '%';
            }

            $monthlyData[] = [
                'month' => 'T' . $m,
                'revenue' => $mRev,
                'revenue_fmt' => number_format($mRev, 0, ',', '.') . ' đ',
                'orders' => $mOrd,
                'orders_fmt' => number_format($mOrd, 0, ',', '.'),
                'aov' => $mAov,
                'aov_fmt' => number_format($mAov, 0, ',', '.') . ' đ',
                'growth' => $growth,
                'growth_val' => $prevRevenue !== null && $prevRevenue > 0 ? round((($mRev - $prevRevenue) / $prevRevenue) * 100, 1) : 0
            ];

            $prevRevenue = $mRev;
        }

        // ── 6. CATEGORY DISTRIBUTION ──
        $dbCategoryRevenue = DonHangChiTiet::join('mon_an', 'don_hang_chi_tiet.MaMonAn', '=', 'mon_an.MaMonAn')
            ->join('don_hang', 'don_hang.MaDonHang', '=', 'don_hang_chi_tiet.MaDonHang')
            ->where('don_hang.TrangThai', 'Hoàn thành')
            ->select('mon_an.DanhMuc', DB::raw('SUM(don_hang_chi_tiet.SoLuong * don_hang_chi_tiet.Gia) as revenue'))
            ->groupBy('mon_an.DanhMuc')
            ->orderByDesc('revenue')
            ->get();

        $categoryDist = [];
        $totalCategoryRevenue = 0;
        foreach ($dbCategoryRevenue as $row) {
            $name = $row->DanhMuc ?: 'Khác';
            $revVal = (float) $row->revenue;
            $totalCategoryRevenue += $revVal;

            $categoryDist[] = [
                'name' => $name,
                'raw_value' => $revVal,
            ];
        }

        if (empty($categoryDist)) {
            $categoryDist = [
                ['name' => 'Cơm', 'raw_value' => 0],
                ['name' => 'Mỳ/Phở', 'raw_value' => 0],
                ['name' => 'Đồ uống', 'raw_value' => 0],
                ['name' => 'Bánh', 'raw_value' => 0],
                ['name' => 'Khác', 'raw_value' => 0],
            ];
        }

        if (count($categoryDist) > 5) {
            $top4 = array_slice($categoryDist, 0, 4);
            $others = array_slice($categoryDist, 4);
            $otherRevenue = array_sum(array_column($others, 'raw_value'));
            $top4[] = [
                'name' => 'Khác',
                'raw_value' => $otherRevenue
            ];
            $categoryDist = $top4;
        }

        foreach ($categoryDist as &$cat) {
            $cat['value'] = $totalCategoryRevenue > 0 ? round(($cat['raw_value'] / $totalCategoryRevenue) * 100, 1) : 0;
        }

        // ── 7. TOP SHOPS BY REVENUE ──
        $topShops = NhaHang::select(
                'nha_hang.TenNhaHang',
                DB::raw('COUNT(DISTINCT don_hang.MaDonHang) as order_count'),
                DB::raw('SUM(don_hang_chi_tiet.Gia * don_hang_chi_tiet.SoLuong) as revenue')
            )
            ->join('mon_an', 'mon_an.MaNhaHang', '=', 'nha_hang.MaNhaHang')
            ->join('don_hang_chi_tiet', 'don_hang_chi_tiet.MaMonAn', '=', 'mon_an.MaMonAn')
            ->join('don_hang', function($j) {
                $j->on('don_hang.MaDonHang', '=', 'don_hang_chi_tiet.MaDonHang')
                  ->where('don_hang.TrangThai', 'Hoàn thành');
            })
            ->groupBy('nha_hang.MaNhaHang', 'nha_hang.TenNhaHang')
            ->orderByDesc('revenue')
            ->limit(7)
            ->get()
            ->map(fn($r) => [
                'name' => $r->TenNhaHang,
                'revenue' => (float)$r->revenue,
                'revenue_fmt' => number_format((float)$r->revenue, 0, ',', '.') . ' đ',
                'orders' => (int)$r->order_count
            ])->toArray();

        // ── 8. YEAR-OVER-YEAR PERIOD COMPARISON (4 WEEKS OF CURRENT MONTH) ──
        $comparison = [];
        for ($w = 1; $w <= 4; $w++) {
            $startDay = ($w - 1) * 7 + 1;
            $endDay = $w * 7;
            if ($w === 4) $endDay = 31;

            $startThisWeek = Carbon::create($thisYear, $thisMonth, $startDay, 0, 0, 0, 'Asia/Ho_Chi_Minh');
            $endThisWeek = Carbon::create($thisYear, $thisMonth, $endDay, 23, 59, 59, 'Asia/Ho_Chi_Minh');

            $startLastWeek = $startThisWeek->copy()->subYear();
            $endLastWeek = $endThisWeek->copy()->subYear();

            $revenueThisWeek = (float) DonHangChiTiet::whereHas('donHang', function ($q) use ($startThisWeek, $endThisWeek) {
                $q->where('TrangThai', 'Hoàn thành')->whereBetween('created_at', [$startThisWeek, $endThisWeek]);
            })->sum(DB::raw('SoLuong * Gia'));

            $revenueLastWeek = (float) DonHangChiTiet::whereHas('donHang', function ($q) use ($startLastWeek, $endLastWeek) {
                $q->where('TrangThai', 'Hoàn thành')->whereBetween('created_at', [$startLastWeek, $endLastWeek]);
            })->sum(DB::raw('SoLuong * Gia'));

            $comparison[] = [
                'week' => 'Tuần ' . $w,
                'this_year' => $revenueThisWeek,
                'last_year' => $revenueLastWeek
            ];
        }

        // ── 9. TOP USERS (LOYAL CUSTOMERS) BY ORDER COUNT ──
        $topUsers = User::select(
                'nguoi_dung.HoTen',
                'nguoi_dung.SoDienThoai',
                DB::raw('COUNT(don_hang.MaDonHang) as order_count'),
                DB::raw('SUM(don_hang.TongTien) as total_spent')
            )
            ->join('don_hang', function($j) {
                $j->on('don_hang.MaNguoiDung', '=', 'nguoi_dung.MaNguoiDung')
                  ->where('don_hang.TrangThai', 'Hoàn thành');
            })
            ->groupBy('nguoi_dung.MaNguoiDung', 'nguoi_dung.HoTen', 'nguoi_dung.SoDienThoai')
            ->orderByDesc('order_count')
            ->limit(5)
            ->get()
            ->map(fn($u) => [
                'name' => $u->HoTen ?: 'Khách hàng',
                'phone' => $u->SoDienThoai ?: 'N/A',
                'orders' => (int)$u->order_count,
                'total_spent' => (float)$u->total_spent,
                'total_spent_fmt' => number_format((float)$u->total_spent, 0, ',', '.') . ' đ'
            ])->toArray();

        return response()->json([
            'success' => true,
            'data' => [
                'kpi' => $kpi,
                'monthly_trend' => $monthlyData,
                'category_dist' => $categoryDist,
                'top_shops' => $topShops,
                'comparison' => $comparison,
                'top_users' => $topUsers
            ]
        ]);
    }
}
