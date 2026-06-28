@extends('seller.layouts.app')

@section('title', 'Dashboard')

@section('content')
<h4 class="fw-bold mb-4">Dashboard <span id="restaurantNameBadge" class="badge bg-secondary fs-6 ms-2" style="display: none;"></span></h4>

<!-- Stats Cards -->
<div class="row g-4 mb-5">
    <!-- Revenue -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm p-4 h-100" style="border-radius: 12px;">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div class="p-3 bg-success rounded-3 text-white">
                    <i class="fas fa-dollar-sign fa-lg"></i>
                </div>
                <span class="text-success small fw-bold" id="revenueGrowth">+0%</span>
            </div>
            <p class="text-muted small mb-1">Doanh thu hôm nay</p>
            <h4 class="fw-bold mb-0" id="statTotalRevenue">
                <i class="fa fa-spinner fa-spin text-muted fs-5"></i>
            </h4>
        </div>
    </div>

    <!-- Orders -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm p-4 h-100" style="border-radius: 12px;">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div class="p-3 bg-primary rounded-3 text-white">
                    <i class="fas fa-shopping-bag fa-lg"></i>
                </div>
                <span class="text-success small fw-bold" id="ordersGrowth">+0%</span>
            </div>
            <p class="text-muted small mb-1">Đơn hàng hôm nay</p>
            <h4 class="fw-bold mb-0" id="statTotalOrders">
                <i class="fa fa-spinner fa-spin text-muted fs-5"></i>
            </h4>
        </div>
    </div>

    <!-- Pending Orders -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm p-4 h-100" style="border-radius: 12px; background-color: #fff5f5;">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div class="p-3 bg-danger rounded-3 text-white">
                    <i class="fas fa-bell fa-lg"></i>
                </div>
            </div>
            <p class="text-danger small fw-bold mb-1">Đơn chờ xác nhận</p>
            <h4 class="fw-bold mb-0 text-danger" id="statTotalFoods">
                <i class="fa fa-spinner fa-spin text-danger fs-5"></i>
            </h4>
        </div>
    </div>

    <!-- Customers -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm p-4 h-100" style="border-radius: 12px;">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div class="p-3 bg-orange rounded-3 text-white" style="background-color: #f97316;">
                    <i class="fas fa-users fa-lg"></i>
                </div>
                <span class="text-success small fw-bold" id="customersGrowth">+0%</span>
            </div>
            <p class="text-muted small mb-1">Khách hàng hôm nay</p>
            <h4 class="fw-bold mb-0" id="statTotalCustomers">
                <i class="fa fa-spinner fa-spin text-muted fs-5"></i>
            </h4>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Recent Orders -->
    <div class="col-12 col-lg-6">
        <div class="card border-0 shadow-sm p-4 h-100" style="border-radius: 12px;">
            <h5 class="fw-bold mb-4">Đơn hàng gần đây</h5>
            <div class="d-flex flex-column gap-3" id="recentOrdersContainer">
                <div class="text-center py-4">
                    <i class="fa fa-spinner fa-spin text-primary fa-2x"></i>
                    <p class="text-muted mt-2">Đang tải đơn hàng...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Selling Foods -->
    <div class="col-12 col-lg-6">
        <div class="card border-0 shadow-sm p-4 h-100" style="border-radius: 12px;">
            <h5 class="fw-bold mb-4">Món bán chạy</h5>
            <div class="d-flex flex-column gap-3" id="topSellingFoodsContainer">
                <div class="text-center py-4">
                    <i class="fa fa-spinner fa-spin text-primary fa-2x"></i>
                    <p class="text-muted mt-2">Đang tải dữ liệu...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('js/api-seller-dashboard.js') }}"></script>
@endsection
