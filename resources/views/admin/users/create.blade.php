@extends('admin.layouts.app')

@section('title', 'Thêm User Mới')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <!-- Main Card -->
                <div class="card shadow-sm border-0 mt-4" style="border-radius: 15px;">
                    <div class="card-body p-4">
                        <!-- Header -->
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="fw-bold mb-0 text-dark">
                                <i class="fas fa-plus text-primary me-2"></i>Thêm User Mới
                            </h4>
                            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-1">
                                <i class="fas fa-arrow-left"></i> Quay lại
                            </a>
                        </div>

                        <!-- Form -->
                        <form id="createUserForm" action="#" method="POST" novalidate autocomplete="off">
                            @csrf
                            
                            <!-- Row 1: Họ tên & Tên đăng nhập -->
                            <div class="row mb-3">
                                <div class="col-md-6 mb-3 mb-md-0">
                                    <label for="HoTen" class="form-label small fw-bold text-muted">Họ và tên <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-user text-muted"></i></span>
                                        <input type="text" class="form-control border-start-0 bg-light" id="HoTen" name="HoTen" placeholder="Nhập họ và tên" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="TenDangNhap" class="form-label small fw-bold text-muted">Tên đăng nhập <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-id-card text-muted"></i></span>
                                        <input type="text" class="form-control border-start-0 bg-light" id="TenDangNhap" name="TenDangNhap" placeholder="Nhập tên đăng nhập" required>
                                    </div>
                                </div>
                            </div>

                            <!-- Row 2: Email & Số điện thoại -->
                            <div class="row mb-3">
                                <div class="col-md-6 mb-3 mb-md-0">
                                    <label for="Email" class="form-label small fw-bold text-muted">Email <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-envelope text-muted"></i></span>
                                        <input type="email" class="form-control border-start-0 bg-light" id="Email" name="Email" placeholder="email@example.com" required autocomplete="off">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="SoDienThoai" class="form-label small fw-bold text-muted">Số điện thoại <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-phone text-muted"></i></span>
                                        <input type="text" class="form-control border-start-0 bg-light" id="SoDienThoai" name="SoDienThoai" placeholder="0901234567" required>
                                    </div>
                                </div>
                            </div>

                            <!-- Row 3: Vai trò & Mật khẩu -->
                            <div class="row mb-4">
                                <div class="col-md-6 mb-3 mb-md-0">
                                    <label for="VaiTro" class="form-label small fw-bold text-muted">Vai trò <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-user-shield text-muted"></i></span>
                                        <select class="form-select border-start-0 bg-light" id="VaiTro" name="VaiTro" required>
                                            <option value="KhachHang">Customer</option>
                                            <option value="NguoiBan">Seller</option>
                                            <option value="QuanTri">Admin</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="MatKhau" class="form-label small fw-bold text-muted">Mật khẩu <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-lock text-muted"></i></span>
                                        <input type="password" class="form-control border-start-0 bg-light" id="MatKhau" name="MatKhau" placeholder="Nhập mật khẩu" required autocomplete="new-password">
                                    </div>
                                </div>
                            </div>

                            <hr class="text-muted opacity-25 mb-4">

                            <!-- Action Buttons -->
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary px-4">Hủy</a>
                                <button type="submit" class="btn btn-primary px-4 d-flex align-items-center gap-1">
                                    <i class="fas fa-check"></i> Lưu
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Script for Validation -->
    <script src="{{ asset('js/user-validator.js') }}?v={{ time() }}"></script>


@endsection
