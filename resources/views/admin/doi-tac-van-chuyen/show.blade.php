@extends('admin.admin')

@section('title', 'Chi tiết đối tác vận chuyển')

@section('page_actions')
    <a href="{{ route('admin.doi-tac-van-chuyen.edit', $doiTacVanChuyen) }}" class="btn btn-soft btn-sm">
        <i class="fas fa-edit"></i> Chỉnh sửa
    </a>
    <a href="{{ route('admin.doi-tac-van-chuyen.index') }}" class="btn btn-soft btn-sm">
        <i class="fas fa-arrow-left"></i> Quay lại
    </a>
@endsection

@section('content')
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <div class="card-surface mb-4">
                <div class="p-3 border-bottom">
                    <div class="d-flex align-items-center">
                        <div class="avatar me-3"
                            style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); width: 50px; height: 50px; font-size: 18px;">
                            {{ substr($doiTacVanChuyen->ten_doi_tac, 0, 1) }}
                        </div>
                        <div>
                            <h5 class="mb-1">{{ $doiTacVanChuyen->ten_doi_tac }}</h5>
                            <div class="d-flex align-items-center gap-3">
                                <small class="text-muted">
                                    Tạo ngày {{ $doiTacVanChuyen->created_at->format('d/m/Y H:i') }}
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-4">
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <h6 class="text-muted mb-2">
                                <i class="fas fa-phone me-2"></i>Số điện thoại liên hệ
                            </h6>
                            <div class="d-flex align-items-center">
                                <span class="fw-bold">{{ $doiTacVanChuyen->so_dien_thoai }}</span>
                                <a href="tel:{{ $doiTacVanChuyen->so_dien_thoai }}"
                                    class="btn btn-sm btn-outline-primary ms-2">
                                    <i class="fas fa-phone"></i>
                                </a>
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <h6 class="text-muted mb-2">
                                <i class="fas fa-envelope me-2"></i>Email liên hệ
                            </h6>
                            <div class="d-flex align-items-center">
                                <span class="fw-bold">{{ $doiTacVanChuyen->email_lien_he }}</span>
                                <a href="mailto:{{ $doiTacVanChuyen->email_lien_he }}"
                                    class="btn btn-sm btn-outline-primary ms-2">
                                    <i class="fas fa-envelope"></i>
                                </a>
                            </div>
                        </div>

                        <div class="col-12 mb-4">
                            <h6 class="text-muted mb-2">
                                <i class="fas fa-map-marker-alt me-2"></i>Địa chỉ trụ sở chính
                            </h6>
                            <p class="fw-bold mb-0">{{ $doiTacVanChuyen->dia_chi_tru_so }}</p>
                        </div>

                        @if($doiTacVanChuyen->ghi_chu)
                            <div class="col-12 mb-4">
                                <h6 class="text-muted mb-2">
                                    <i class="fas fa-sticky-note me-2"></i>Ghi chú
                                </h6>
                                <div class="p-3 bg-light rounded">
                                    {{ $doiTacVanChuyen->ghi_chu }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card-surface mb-4">
                <div class="p-3 border-bottom">
                    <h6 class="mb-0">
                        <i class="fas fa-cogs me-2"></i>Thao tác
                    </h6>
                </div>
                <div class="p-3">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.doi-tac-van-chuyen.edit', $doiTacVanChuyen) }}" class="btn btn-primary">
                            <i class="fas fa-edit me-2"></i>Chỉnh sửa thông tin
                        </a>

                        <hr>

                        <form action="{{ route('admin.doi-tac-van-chuyen.destroy', $doiTacVanChuyen) }}" method="POST"
                            onsubmit="return confirm('Bạn có chắc muốn xóa đối tác vận chuyển này? Thao tác này không thể hoàn tác.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger w-100">
                                <i class="fas fa-trash me-2"></i>Xóa đối tác
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="card-surface">
                <div class="p-3 border-bottom">
                    <h6 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>Thông tin hệ thống
                    </h6>
                </div>
                <div class="p-3">
                    <div class="row g-2">
                        <div class="col-6">
                            <small class="text-muted">ID:</small>
                            <div class="fw-bold">#{{ $doiTacVanChuyen->id }}</div>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Trạng thái:</small>
                            <div>
                                @if($doiTacVanChuyen->trang_thai === 'Hoạt động')
                                    <span class="badge bg-success">Hoạt động</span>
                                @else
                                    <span class="badge bg-warning">Tạm dừng</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-12 mt-3">
                            <small class="text-muted">Ngày tạo:</small>
                            <div class="fw-bold">{{ $doiTacVanChuyen->created_at->format('d/m/Y H:i:s') }}</div>
                        </div>
                        @if($doiTacVanChuyen->updated_at != $doiTacVanChuyen->created_at)
                            <div class="col-12">
                                <small class="text-muted">Cập nhật cuối:</small>
                                <div class="fw-bold">{{ $doiTacVanChuyen->updated_at->format('d/m/Y H:i:s') }}</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .avatar {
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
        }

        .badge {
            font-size: 0.75rem;
        }

        .btn-outline-primary:hover,
        .btn-outline-secondary:hover,
        .btn-outline-danger:hover {
            transform: translateY(-1px);
            transition: all 0.2s ease;
        }
    </style>
@endsection