@extends('admin.layouts.app')

@section('title', 'Chi tiết Danh mục')

@section('content')
<div class="container-fluid">
    <!-- Header Banner -->
    <div class="card mb-4 border-0 shadow-sm" style="background: linear-gradient(135deg, #a855f7 0%, #6366f1 100%); border-radius: 15px;">
        <div class="card-body p-4 d-flex justify-content-between align-items-center text-white">
            <div>
                <h3 class="fw-bold mb-1"><i class="fas fa-eye me-2"></i>Chi Tiết Danh Mục</h3>
                <p class="mb-0 opacity-75">Xem thông tin chi tiết danh mục ẩm thực hệ thống</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.danh-muc.edit', $id) }}" class="btn btn-warning btn-sm text-dark fw-bold">
                    <i class="fas fa-edit me-1"></i> Chỉnh sửa
                </a>
                <a href="{{ route('admin.danh-muc.index') }}" class="btn btn-light btn-sm text-dark fw-bold">
                    <i class="fas fa-arrow-left me-1"></i> Quay lại danh sách
                </a>
            </div>
        </div>
    </div>

    <!-- Details Container -->
    <div class="card shadow-sm border-0 mb-4" style="border-radius: 15px;">
        <div class="card-body p-4" id="categoryDetailContainer">
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status"></div>
                <p class="text-muted mt-2 mb-0">Đang tải chi tiết danh mục...</p>
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
<script>
    window.categoryId = {{ $id }};
</script>
<script src="{{ asset('js/api-admin-danh-muc-detail.js') }}"></script>
@endsection
