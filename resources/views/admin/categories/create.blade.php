@extends('admin.layouts.app')

@section('title', 'Thêm Danh mục mới')

@section('content')
<div class="container-fluid">
    <div class="row mt-4">
        <!-- Form Section -->
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 mb-4" style="border-radius: 15px;">
                <div class="card-body p-4">
                    <!-- Header -->
                    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                        <h4 class="fw-bold mb-0 text-dark">
                            <i class="fas fa-plus-circle text-primary me-2"></i>Thêm Danh Mục Mới
                        </h4>
                        <a href="{{ route('admin.danh-muc.index') }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-1">
                            <i class="fas fa-arrow-left"></i> Quay lại
                        </a>
                    </div>
                    
                    <form id="createCategoryForm">
                        <div class="mb-3">
                            <label for="TenDanhMuc" class="form-label text-dark fw-semibold">Tên danh mục <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-lg" id="TenDanhMuc" name="TenDanhMuc" placeholder="Ví dụ: Cơm, Bún, Phở, Trà sữa..." required>
                            <div class="form-text">Hãy chọn một cái tên ngắn gọn và dễ nhận biết nhất.</div>
                        </div>

                        <div class="mb-3">
                            <label for="Slug" class="form-label text-dark fw-semibold">Slug danh mục</label>
                            <input type="text" class="form-control" id="Slug" name="Slug" placeholder="Ví dụ: com, bun, pho, do-uong (để trống để tự động tạo)">
                            <div class="form-text">Chuỗi định danh URL viết liền không dấu, ngăn cách bởi gạch ngang (ví dụ: do-an-vat).</div>
                        </div>

                        <div class="mb-3">
                            <label for="MoTa" class="form-label text-dark fw-semibold">Mô tả danh mục</label>
                            <textarea class="form-control" id="MoTa" name="MoTa" rows="3" placeholder="Nhập một vài mô tả tóm tắt cho danh mục này..."></textarea>
                            <div class="form-text">Tối đa 255 ký tự. Mô tả giúp hiển thị chi tiết khi người dùng lọc món ăn.</div>
                        </div>

                        <div class="mb-3">
                            <label for="HinhAnhFile" class="form-label text-dark fw-semibold">Chọn hình ảnh từ thiết bị</label>
                            <input type="file" class="form-control" id="HinhAnhFile" name="HinhAnhFile" accept="image/*">
                            <div class="form-text">Hỗ trợ JPG, JPEG, PNG, GIF (Kích thước tối đa: 2MB).</div>
                        </div>

                        <div class="mb-3">
                            <label for="HinhAnh" class="form-label text-dark fw-semibold">Hoặc Đường dẫn hình ảnh (URL)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fas fa-image"></i></span>
                                <input type="text" class="form-control" id="HinhAnh" name="HinhAnh" placeholder="Ví dụ: /images/categories/default.png">
                            </div>
                            <div class="form-text">Được cập nhật tự động khi tải tệp lên hoặc có thể điền link URL tùy chọn.</div>
                        </div>

                        <div class="mb-4">
                            <label for="TrangThai" class="form-label text-dark fw-semibold">Trạng thái hoạt động <span class="text-danger">*</span></label>
                            <select class="form-select form-select-lg" id="TrangThai" name="TrangThai" required>
                                <option value="Hoạt động" selected>Hoạt động (Hiển thị rộng rãi)</option>
                                <option value="Khóa">Tạm khóa (Ẩn khỏi ứng dụng)</option>
                            </select>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary px-4 py-2 fw-semibold" style="border-radius:8px;">
                                <i class="fas fa-save me-1"></i> Lưu lại
                            </button>
                            <a href="{{ route('admin.danh-muc.index') }}" class="btn btn-outline-secondary px-4 py-2" style="border-radius:8px;">
                                Hủy bỏ
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Preview Section -->
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 text-center p-4 mb-4" style="border-radius: 15px; background: var(--card);">
                <h5 class="fw-bold text-muted mb-3"><i class="fas fa-eye me-1"></i>Ảnh xem trước</h5>
                
                <div class="mx-auto shadow-sm border mb-3" style="width: 150px; height: 150px; border-radius: 15px; overflow: hidden; background: #f3f4f6; display: flex; align-items: center; justify-content: center; border-color: var(--border);">
                    <img id="previewImage" src="https://placehold.co/150x150?text=Preview" alt="Xem trước hình ảnh" style="width: 100%; height: 100%; object-fit: cover;">
                </div>
                
                <p class="text-muted small mb-0 px-2">Hình ảnh xem trước được tải tự động từ liên kết URL bạn cung cấp bên cạnh.</p>
            </div>
        </div>
    </div>
</div>

<!-- Toast -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index:1100">
    <div id="actionToast" class="toast align-items-center text-white border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body fw-bold" id="toastMessage">Thông báo</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/api-admin-danh-muc-detail.js') }}"></script>
@endsection
