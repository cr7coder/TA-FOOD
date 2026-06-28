<?php

namespace App\Http\Controllers\Web\Seller;

use App\Http\Controllers\Controller;

use App\Models\DonHang;
use App\Models\DonHangChiTiet;
use App\Models\MonAn;
use App\Models\NhaHang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SellerDashboardController extends Controller
{
    public function index()
    {
        return view('seller.dashboard.index');


    }
}

