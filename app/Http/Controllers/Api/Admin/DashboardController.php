<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\DonHang;
use App\Models\MonAn;
use App\Models\NhaHang;
use App\Models\User;
use App\Models\DanhMuc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
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

        $now = Carbon::now();
        $thisMonth = $now->month;
        $lastMonth = $now->copy()->subMonth()->month;
        $thisYear = $now->year;
        $lastYear = $now->copy()->subMonth()->year;

        // ── KPI Cards ────────────────────────────────────────────────────────────
        // Revenue: sum of all completed orders
        $revenueThis = DonHang::whereIn('TrangThai', ['Hoàn thành'])
            ->whereYear('created_at', $thisYear)->whereMonth('created_at', $thisMonth)
            ->sum('TongTien');
        $revenueLast = DonHang::whereIn('TrangThai', ['Hoàn thành'])
            ->whereYear('created_at', $lastYear)->whereMonth('created_at', $lastMonth)
            ->sum('TongTien');
        $revenueTotal = DonHang::whereIn('TrangThai', ['Hoàn thành'])->sum('TongTien');
        $revenueChange = $revenueLast > 0 ? round((($revenueThis - $revenueLast) / $revenueLast) * 100, 1) : ($revenueThis > 0 ? 100 : 0);

        // Orders
        $ordersThis = DonHang::whereYear('created_at', $thisYear)->whereMonth('created_at', $thisMonth)->count();
        $ordersLast = DonHang::whereYear('created_at', $lastYear)->whereMonth('created_at', $lastMonth)->count();
        $ordersTotal = DonHang::count();
        $ordersChange = $ordersLast > 0 ? round((($ordersThis - $ordersLast) / $ordersLast) * 100, 1) : ($ordersThis > 0 ? 100 : 0);

        // Users
        $usersThis = User::whereYear('created_at', $thisYear)->whereMonth('created_at', $thisMonth)->count();
        $usersLast = User::whereYear('created_at', $lastYear)->whereMonth('created_at', $lastMonth)->count();
        $usersTotal = User::count();
        $usersChange = $usersLast > 0 ? round((($usersThis - $usersLast) / $usersLast) * 100, 1) : ($usersThis > 0 ? 100 : 0);

        // Restaurants
        $restaurantsThis = NhaHang::whereYear('created_at', $thisYear)->whereMonth('created_at', $thisMonth)->count();
        $restaurantsLast = NhaHang::whereYear('created_at', $lastYear)->whereMonth('created_at', $lastMonth)->count();
        $restaurantsTotal = NhaHang::count();
        $restaurantsChange = $restaurantsLast > 0 ? round((($restaurantsThis - $restaurantsLast) / $restaurantsLast) * 100, 1) : ($restaurantsThis > 0 ? 100 : 0);

        // ── Monthly Chart (12 months) ─────────────────────────────────────────────
        $monthlyData = [];
        for ($m = 1; $m <= 12; $m++) {
            $revenue = DonHang::whereIn('TrangThai', ['Hoàn thành'])
                ->whereYear('created_at', $thisYear)->whereMonth('created_at', $m)
                ->sum('TongTien');
            $orders = DonHang::whereYear('created_at', $thisYear)->whereMonth('created_at', $m)->count();
            $monthlyData[] = [
                'month' => 'T' . $m,
                'revenue' => (float) $revenue,
                'orders' => (int) $orders,
            ];
        }

        // ── Category Distribution (Pie chart by sales volume) ───────────────────
        $categoryDist = \App\Models\DonHangChiTiet::join('mon_an', 'don_hang_chi_tiet.MaMonAn', '=', 'mon_an.MaMonAn')
            ->join('don_hang', 'don_hang.MaDonHang', '=', 'don_hang_chi_tiet.MaDonHang')
            ->where('don_hang.TrangThai', 'Hoàn thành')
            ->select('mon_an.DanhMuc', DB::raw('SUM(don_hang_chi_tiet.SoLuong) as total'))
            ->groupBy('mon_an.DanhMuc')
            ->orderByDesc('total')
            ->get()
            ->map(fn($r) => [
                'name' => $r->DanhMuc ?: 'Khác',
                'value' => (int) $r->total
            ]);

        // ── Top 5 Restaurants by Revenue ─────────────────────────────────────────
        $topRestaurants = NhaHang::select(
                'nha_hang.MaNhaHang',
                'nha_hang.TenNhaHang',
                DB::raw('COUNT(DISTINCT don_hang.MaDonHang) as order_count'),
                DB::raw('SUM(don_hang_chi_tiet.Gia * don_hang_chi_tiet.SoLuong) as revenue')
            )
            ->join('mon_an', 'mon_an.MaNhaHang', '=', 'nha_hang.MaNhaHang')
            ->join('don_hang_chi_tiet', 'don_hang_chi_tiet.MaMonAn', '=', 'mon_an.MaMonAn')
            ->join('don_hang', function($j) {
                $j->on('don_hang.MaDonHang', '=', 'don_hang_chi_tiet.MaDonHang')
                  ->whereIn('don_hang.TrangThai', ['Hoàn thành']);
            })
            ->groupBy('nha_hang.MaNhaHang', 'nha_hang.TenNhaHang')
            ->orderByDesc('revenue')
            ->limit(5)
            ->get()
            ->map(fn($r) => [
                'id'          => $r->MaNhaHang,
                'name'        => $r->TenNhaHang,
                'image'       => null,
                'order_count' => (int) $r->order_count,
                'revenue'     => (float) $r->revenue,
                'revenue_fmt' => number_format((float) $r->revenue, 0, ',', '.') . ' đ',
            ]);

        // ── Recent Orders (last 7) ────────────────────────────────────────────────
        $uiStatusMap = [
            'Chờ xử lý'     => ['label' => 'Chờ xác nhận', 'color' => 'warning'],
            'Đã xác nhận'   => ['label' => 'Đã xác nhận',  'color' => 'info'],
            'Đang chuẩn bị' => ['label' => 'Đang chuẩn bị','color' => 'primary'],
            'Đang giao'     => ['label' => 'Đang giao',     'color' => 'info'],
            'Hoàn thành'    => ['label' => 'Hoàn thành',    'color' => 'success'],
            'Hủy'           => ['label' => 'Đã hủy',        'color' => 'danger'],
        ];

        $recentOrders = DonHang::with(['nguoiDung'])
            ->orderByDesc('created_at')
            ->limit(7)
            ->get()
            ->map(function($o) use ($uiStatusMap) {
                $st = $uiStatusMap[$o->TrangThai] ?? ['label' => $o->TrangThai, 'color' => 'secondary'];
                return [
                    'id'       => $o->MaDonHang,
                    'code'     => 'ORD' . str_pad($o->MaDonHang, 5, '0', STR_PAD_LEFT),
                    'customer' => $o->TenKhachHang ?: ($o->nguoiDung->HoTen ?? 'Khách lẻ'),
                    'amount'   => number_format((float) $o->TongTien, 0, ',', '.') . ' đ',
                    'status'   => $st['label'],
                    'color'    => $st['color'],
                    'date'     => $o->created_at->format('d/m/Y H:i'),
                ];
            });

        return response()->json([
            'success' => true,
            'data'    => [
                'kpi' => [
                    'revenue'     => [
                        'total_all_time'   => $revenueTotal,
                        'total_this_month'  => $revenueThis,
                        'fmt_all_time'     => number_format((float)$revenueTotal, 0, ',', '.'),
                        'fmt_this_month'    => number_format((float)$revenueThis, 0, ',', '.'),
                        'change'            => $revenueChange
                    ],
                    'orders'      => [
                        'total_all_time'   => $ordersTotal,
                        'total_this_month'  => $ordersThis,
                        'change'            => $ordersChange
                    ],
                    'users'       => [
                        'total_all_time'   => $usersTotal,
                        'total_this_month'  => $usersThis,
                        'change'            => $usersChange
                    ],
                    'restaurants' => [
                        'total_all_time'   => $restaurantsTotal,
                        'total_this_month'  => $restaurantsThis,
                        'change'            => $restaurantsChange
                    ],
                ],
                'monthly'        => $monthlyData,
                'category_dist'  => $categoryDist,
                'top_restaurants'=> $topRestaurants,
                'recent_orders'  => $recentOrders,
            ]
        ]);
    }
}
