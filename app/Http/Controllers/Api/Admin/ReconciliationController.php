<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\DonHang;
use App\Models\NhaHang;
use App\Models\DoiTacVanChuyen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReconciliationController extends Controller
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

        $monthStr = $request->query('month'); // format: YYYY-MM
        if (!$monthStr) {
            $monthStr = Carbon::now('Asia/Ho_Chi_Minh')->format('Y-m');
        }

        try {
            $date = Carbon::createFromFormat('Y-m', $monthStr);
        } catch (\Exception $e) {
            $date = Carbon::now('Asia/Ho_Chi_Minh');
            $monthStr = $date->format('Y-m');
        }

        $startOfMonth = $date->copy()->startOfMonth();
        $endOfMonth = $date->copy()->endOfMonth();

        // Fetch completed orders in the target month
        $orders = DonHang::with(['chiTiet.monAn.nhaHang', 'doiTacVanChuyen', 'giamGia'])
            ->where('TrangThai', 'Hoàn thành')
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->get();

        // 1. Calculate stats per Seller (Restaurant)
        $sellerData = [];
        $restaurants = NhaHang::all();
        foreach ($restaurants as $restaurant) {
            $commissionRate = (float)($restaurant->commission_rate ?? 15.00);
            $sellerData[$restaurant->MaNhaHang] = [
                'id' => $restaurant->MaNhaHang,
                'name' => $restaurant->TenNhaHang,
                'owner' => $restaurant->nguoiDung->HoTen ?? 'N/A',
                'commission_rate' => $commissionRate,
                'orders_count' => 0,
                'food_revenue' => 0,
                'commission_amount' => 0,
                'shipping_fee' => 0,
                'carrier_cost' => 0,
                'shipping_diff' => 0,
                'total_merchant_owed' => 0,
                'status' => Cache::get("payout_seller_{$restaurant->MaNhaHang}_{$monthStr}", false) ? 'Paid' : 'Unpaid'
            ];
        }

        // 2. Calculate stats per Carrier
        $carrierData = [];
        $carriers = DoiTacVanChuyen::all();
        foreach ($carriers as $carrier) {
            $carrierData[$carrier->id] = [
                'id' => $carrier->id,
                'name' => $carrier->ten_doi_tac,
                'orders_count' => 0,
                'carrier_cost' => 0,
                'status' => Cache::get("payout_carrier_{$carrier->id}_{$monthStr}", false) ? 'Paid' : 'Unpaid'
            ];
        }

        // Aggregate orders
        $totalCustomerPaid  = 0;
        $totalSellerOwed    = 0;
        $totalCarrierOwed   = 0;
        $totalCommission    = 0; // (+) Revenue: commission % from seller food revenue
        $totalVoucherCost   = 0; // (-) Cost: voucher discounts admin absorbs
        $totalShippingMargin = 0; // net: (shipFee collected from customer) - (actual carrier cost)
                                  //      positive = admin earns on ship, negative = admin subsidises ship

        foreach ($orders as $order) {
            $tamTinh       = $order->chiTiet->sum(fn($i) => (float)$i->Gia * $i->SoLuong);
            $phiVanChuyen  = (float)$order->PhiVanChuyen;
            $tongTien      = (float)$order->TongTien;
            $carrierCost   = $this->getOrderCarrierCost($order);

            $totalCustomerPaid += $tongTien;

            // Voucher subsidy = (food + ship) that customer SHOULD have paid minus what they DID pay
            $grossBeforeVoucher = $tamTinh + $phiVanChuyen;
            $voucherSubsidy     = max(0, $grossBeforeVoucher - $tongTien);
            $totalVoucherCost  += $voucherSubsidy;

            // Shipping margin: how much admin keeps/loses on delivery
            $shippingMargin      = $phiVanChuyen - $carrierCost;
            $totalShippingMargin += $shippingMargin;

            // Get restaurant from the first food item
            $firstDetail  = $order->chiTiet->first();
            $restaurantId = $firstDetail->monAn->MaNhaHang ?? null;

            if ($restaurantId && isset($sellerData[$restaurantId])) {
                $commissionRate   = $sellerData[$restaurantId]['commission_rate'];
                $commissionAmount = round($tamTinh * ($commissionRate / 100), 0);
                $netFoodRevenue   = $tamTinh - $commissionAmount;

                // What admin owes seller = net food revenue + pass-through shipping margin
                $owed = $netFoodRevenue + $shippingMargin;

                $sellerData[$restaurantId]['orders_count']++;
                $sellerData[$restaurantId]['food_revenue']      += $tamTinh;
                $sellerData[$restaurantId]['commission_amount'] += $commissionAmount;
                $sellerData[$restaurantId]['shipping_fee']      += $phiVanChuyen;
                $sellerData[$restaurantId]['carrier_cost']      += $carrierCost;
                $sellerData[$restaurantId]['shipping_diff']     += $shippingMargin;
                $sellerData[$restaurantId]['total_merchant_owed'] += $owed;
                $totalSellerOwed += $owed;
                $totalCommission += $commissionAmount;
            }

            // Carrier payable
            if ($order->MaDoiTacVanChuyen && isset($carrierData[$order->MaDoiTacVanChuyen])) {
                $carrierData[$order->MaDoiTacVanChuyen]['orders_count']++;
                $carrierData[$order->MaDoiTacVanChuyen]['carrier_cost'] += $carrierCost;
                $totalCarrierOwed += $carrierCost;
            }
        }

        // Filter active parties
        $activeSellers  = array_values(array_filter($sellerData, fn($s) => $s['orders_count'] > 0 || $s['status'] === 'Paid'));
        $activeCarriers = array_values(array_filter($carrierData, fn($c) => $c['orders_count'] > 0 || $c['status'] === 'Paid'));

        /**
         * CÔNG THỨC THỰC THU HỆ THỐNG
         * ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
         *  Thu (+):  Hoa hồng = Σ (tiền đồ ăn × commission_rate%)
         *  Chi (−):  Voucher   = Σ (giảm giá admin chịu)
         *
         *  Phí ship: TRUNG LẬP (admin thu từ khách rồi chuyển hết
         *            cho Seller+Carrier — không giữ lại đồng nào)
         *
         *  → admin_net = commission − voucher_subsidy
         *             = tổng_khách − tổng_trả_seller − tổng_trả_vc
         * ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
         */
        $adminNet = $totalCustomerPaid - $totalSellerOwed - $totalCarrierOwed;

        return response()->json([
            'success' => true,
            'month'   => $monthStr,
            'summary' => [
                'total_orders'         => $orders->count(),
                'total_customer_paid'  => $totalCustomerPaid,
                'total_seller_owed'    => $totalSellerOwed,
                'total_carrier_owed'   => $totalCarrierOwed,

                // Breakdown of Admin Net:
                // Net = commission_earned − voucher_subsidy
                // (shipping is a pure pass-through, always ~0 for admin)
                'commission_earned'    => $totalCommission,   // (+) main revenue
                'voucher_subsidy'      => $totalVoucherCost,  // (−) admin cost

                'admin_net_commission' => $adminNet,
            ],
            'sellers'  => $activeSellers,
            'carriers' => $activeCarriers,
        ]);
    }

    public function payout(Request $request)
    {
        if ($response = $this->checkAdmin()) return $response;

        $request->validate([
            'type' => 'required|in:seller,carrier',
            'id' => 'required',
            'month' => 'required|string',
            'status' => 'required|boolean'
        ]);

        $type = $request->input('type');
        $id = $request->input('id');
        $monthStr = $request->input('month');
        $status = $request->input('status');

        $cacheKey = "payout_{$type}_{$id}_{$monthStr}";
        
        if ($status) {
            Cache::put($cacheKey, true, 86400 * 365); // Keep for a year
        } else {
            Cache::forget($cacheKey);
        }

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật trạng thái thanh toán đối soát thành công.'
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
