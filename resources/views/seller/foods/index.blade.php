@extends('seller.layouts.app')
@section('title', 'Quản lý món ăn')
@section('content')
    <div class="container-fluid">
        <div class="card shadow mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Danh sách món ăn</h6>
                <a href="{{ route('seller.foods.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Thêm món
                </a>
            </div>
            <div class="card-body">
                <form id="foods-search-form" class="row g-2 mb-3">
                    <div class="col-md-3">
                        <input type="text" id="search-input" name="search" class="form-control form-control-sm" placeholder="Tìm tên món..."
                            value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-sm btn-primary w-100"><i class="fas fa-search"></i> Lọc</button>
                    </div>
                    <div class="col-md-2">
                        <button type="button" id="reset-btn" class="btn btn-sm btn-outline-secondary w-100">
                            <i class="fas fa-redo"></i> Reset
                        </button>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-sm table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Mã</th>
                                <th>Tên món</th>
                                <th>Nhà hàng</th>
                                <th>Danh mục</th>
                                <th>Giá</th>
                                <th>Trạng thái</th>
                                <th>Ảnh</th>
                                <th width="90">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody id="foods-table-body">
                            <!-- Data will be loaded here via API -->
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <p class="mt-2 text-muted">Đang tải danh sách món ăn...</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div id="pagination-container" class="d-flex justify-content-end mt-3">
                    <!-- Pagination will be rendered here -->
                </div>
            </div>
        </div>
    </div>

    <!-- Food Detail Modal -->
    <div class="modal fade" id="food-detail-modal" tabindex="-1" aria-labelledby="foodDetailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
                <div class="modal-header border-0 pb-0" style="background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);">
                    <h5 class="modal-title fw-bold text-dark" id="foodDetailModalLabel">
                        <i class="fas fa-utensils me-2 text-primary"></i> Chi tiết món ăn
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4" id="food-detail-modal-body">
                    <!-- Loading state initially -->
                    <div class="text-center py-5" id="modal-loading-spinner">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2 text-muted">Đang tải chi tiết món ăn...</p>
                    </div>
                    
                    <!-- Content (hidden initially) -->
                    <div class="row g-4 d-none" id="modal-food-content">
                        <!-- Left Column: Image Showcase -->
                        <div class="col-md-5">
                            <div class="position-relative rounded-4 overflow-hidden shadow-sm" style="height: 250px;">
                                <img id="detail-food-img" src="" alt="Tên món ăn" class="w-100 h-100" style="object-fit: cover;">
                                <div id="detail-food-status" class="position-absolute" style="top: 15px; left: 15px;">
                                    <!-- Status badge will go here -->
                                </div>
                            </div>
                            
                            <!-- Rating & Reviews Statistics -->
                            <div class="mt-3 p-3 bg-light rounded-4 d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <div class="text-warning me-2">
                                        <i class="fas fa-star fa-lg"></i>
                                    </div>
                                    <div>
                                        <span id="detail-food-rating" class="fw-bold fs-5">0.0</span>
                                        <span class="text-muted small">/5</span>
                                    </div>
                                </div>
                                <div class="text-muted small">
                                    <i class="fas fa-comment-alt me-1"></i> <span id="detail-food-reviews-count">0</span> đánh giá
                                </div>
                            </div>
                        </div>

                        <!-- Right Column: Food Metadata & Description -->
                        <div class="col-md-7">
                            <h3 id="detail-food-name" class="fw-bold text-dark mb-1">Tên món ăn</h3>
                            <div class="mb-3">
                                <span id="detail-food-category" class="badge bg-primary px-3 py-2 rounded-pill">Danh mục</span>
                            </div>

                            <div class="card border-0 bg-light rounded-4 p-3 mb-3">
                                <div class="row g-3">
                                    <div class="col-6">
                                        <div class="text-muted small mb-1"><i class="fas fa-coins me-1"></i> Giá bán</div>
                                        <div id="detail-food-price" class="fw-bold fs-4 text-primary">0 đ</div>
                                    </div>
                                    <div class="col-6">
                                        <div class="text-muted small mb-1"><i class="fas fa-store me-1"></i> Nhà hàng</div>
                                        <div id="detail-food-restaurant" class="fw-bold text-dark text-truncate" style="max-width: 100%;">Tên nhà hàng</div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="fw-bold text-dark mb-2"><i class="fas fa-align-left me-1 text-muted"></i> Mô tả món ăn</div>
                                <p id="detail-food-desc" class="text-muted small lh-lg mb-0" style="max-height: 120px; overflow-y: auto;">Chưa có mô tả cho món ăn này.</p>
                            </div>

                            <div class="border-top pt-3 text-muted small d-flex justify-content-between">
                                <span><i class="fas fa-calendar-alt me-1"></i> Tạo ngày: <span id="detail-food-created">--/--/----</span></span>
                                <span><i class="fas fa-edit me-1"></i> Cập nhật: <span id="detail-food-updated">--/--/----</span></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-light p-3">
                    <a id="detail-food-edit-btn" href="#" class="btn btn-warning btn-sm px-3 rounded-pill fw-semibold text-dark">
                        <i class="fas fa-edit me-1"></i> Chỉnh sửa món
                    </a>
                    <button type="button" class="btn btn-secondary btn-sm px-4 rounded-pill" data-bs-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            #food-detail-modal .modal-content {
                box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15) !important;
                border: 1px solid rgba(0, 0, 0, 0.05);
                backdrop-filter: saturate(180%) blur(10px);
            }
            #food-detail-modal .modal-dialog {
                transition: transform 0.3s ease-out;
            }
            #food-detail-modal.fade .modal-dialog {
                transform: scale(0.95) translateY(10px);
            }
            #food-detail-modal.show .modal-dialog {
                transform: scale(1) translateY(0);
            }
            .status-badge-premium {
                padding: 6px 14px;
                border-radius: 50px;
                font-size: 0.8rem;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: 0.05em;
                box-shadow: 0 4px 10px rgba(0,0,0,0.15);
                backdrop-filter: blur(8px);
                border: 1px solid rgba(255, 255, 255, 0.25);
            }
            .status-badge-premium.active {
                background-color: rgba(46, 204, 113, 0.85);
                color: #fff;
            }
            .status-badge-premium.inactive {
                background-color: rgba(149, 165, 166, 0.85);
                color: #fff;
            }
            .status-badge-premium.hidden {
                background-color: rgba(241, 196, 15, 0.85);
                color: #2c3e50;
            }
            #detail-food-desc::-webkit-scrollbar {
                width: 4px;
            }
            #detail-food-desc::-webkit-scrollbar-thumb {
                background: #cbd5e1;
                border-radius: 4px;
            }
            #detail-food-desc::-webkit-scrollbar-track {
                background: transparent;
            }
        </style>
    @endpush

    <script src="{{ asset('js/api-seller-foods.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Target both .auto-dismiss and common bootstrap alerts
            const alerts = document.querySelectorAll('.alert-success, .alert-info, .auto-dismiss');
            alerts.forEach(function (alert) {
                setTimeout(function () {
                    alert.style.transition = 'all 0.5s ease';
                    alert.style.opacity = '0';
                    alert.style.transform = 'translateY(-10px)';
                    setTimeout(function () {
                        alert.remove();
                    }, 500);
                }, 3000);
            });
        });
    </script>
@endsection