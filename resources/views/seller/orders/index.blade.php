@extends('seller.layouts.app')
@section('title', 'Quản lý Đơn hàng')

@push('styles')
    <link href="{{ asset('css/seller-orders.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="orders-container">
        <!-- Header -->
        <div class="orders-header">
            <h1>Đơn hàng</h1>
            <div class="filter-wrapper">
                <select id="order-status-filter" class="status-filter">
                    <option value="Tất cả">Tất cả</option>
                    <option value="Chờ xác nhận">Chờ xác nhận</option>
                    <option value="Đã xác nhận">Đã xác nhận</option>
                    <option value="Đang chuẩn bị">Đang chuẩn bị</option>
                    <option value="Đang giao">Đang giao</option>
                    <option value="Hoàn thành">Hoàn thành</option>
                    <option value="Đã hủy">Đã hủy</option>
                </select>
            </div>
        </div>

        <!-- Main Card -->
        <div class="order-card">
            <div class="table-responsive">
                <table class="order-table">
                    <thead>
                        <tr>
                            <th>Mã đơn</th>
                            <th>Khách hàng</th>
                            <th>Món ăn</th>
                            <th>Tổng tiền</th>
                            <th>Trạng thái</th>
                            <th>Thanh toán</th>
                            <th>Thời gian</th>
                            <th class="text-center">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody id="orders-table-body">
                        <!-- Loading skeleton -->
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="mt-2 text-muted">Đang tải danh sách đơn hàng...</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <!-- Pagination Wrapper -->
            <div class="orders-pagination-container px-4 py-3 border-top d-flex justify-content-between align-items-center">
                <div class="pagination-info text-muted small">
                    Đang hiển thị dòng <span id="pagination-start">0</span> đến <span id="pagination-end">0</span> của <span id="pagination-total">0</span> đơn hàng
                </div>
                <nav aria-label="Order navigation">
                    <ul class="pagination pagination-sm m-0" id="pagination-buttons">
                        <!-- Dynamically populated buttons -->
                    </ul>
                </nav>
            </div>
        </div>

        <!-- Empty State -->
        <div id="empty-state" class="text-center py-5 d-none">
            <img src="https://cdni.iconscout.com/illustration/premium/thumb/no-data-found-8867280-7265556.png" 
                alt="No orders" style="max-width: 200px; opacity: 0.6;">
            <p class="mt-3 text-muted">Không có đơn hàng nào trong danh sách này.</p>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/api-seller-orders.js') }}"></script>
@endpush
