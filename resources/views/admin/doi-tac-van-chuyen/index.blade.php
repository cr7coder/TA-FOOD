@extends('admin.admin')

@section('title', 'Quản lý Đối tác vận chuyển')

@section('page_actions')
    <a href="{{ route('admin.doi-tac-van-chuyen.create') }}" class="btn btn-soft btn-sm">
        <i class="fas fa-plus"></i> Thêm đối tác
    </a>
@endsection

@section('content')
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card-surface">
        <div class="p-3 border-bottom">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-truck me-2 text-primary"></i>
                    Danh sách đối tác vận chuyển
                </h5>
                <span class="badge bg-primary">{{ $doiTacs->total() }} đối tác</span>
            </div>
        </div>

        @if($doiTacs->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">#</th>
                            <th width="25%">Tên đối tác</th>
                            <th width="20%">Số điện thoại</th>
                            <th width="30%">Địa chỉ trụ sở</th>
                            <th width="15%">Email liên hệ</th>
                            <th width="5%">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($doiTacs as $index => $doiTac)
                            <tr>
                                <td>{{ $doiTacs->firstItem() + $index }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar me-2"
                                            style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                            {{ substr($doiTac->ten_doi_tac, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold">{{ $doiTac->ten_doi_tac }}</div>
                                            <small class="text-muted">{{ $doiTac->created_at->format('d/m/Y') }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <i class="fas fa-phone text-muted me-1"></i>
                                    {{ $doiTac->so_dien_thoai }}
                                </td>
                                <td>
                                    <i class="fas fa-map-marker-alt text-muted me-1"></i>
                                    {{ Str::limit($doiTac->dia_chi_tru_so, 40) }}
                                </td>
                                <td>
                                    <i class="fas fa-envelope text-muted me-1"></i>
                                    {{ $doiTac->email_lien_he }}
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a class="dropdown-item"
                                                    href="{{ route('admin.doi-tac-van-chuyen.show', $doiTac) }}">
                                                    <i class="fas fa-eye me-2"></i>Xem chi tiết
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item"
                                                    href="{{ route('admin.doi-tac-van-chuyen.edit', $doiTac) }}">
                                                    <i class="fas fa-edit me-2"></i>Chỉnh sửa
                                                </a>
                                            </li>
                                            <li>
                                                <hr class="dropdown-divider">
                                            </li>
                                            <li>
                                                <form action="{{ route('admin.doi-tac-van-chuyen.destroy', $doiTac) }}"
                                                    method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa đối tác này?')"
                                                    class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger">
                                                        <i class="fas fa-trash me-2"></i>Xóa
                                                    </button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($doiTacs->hasPages())
                <div class="p-3 border-top">
                    {{ $doiTacs->links() }}
                </div>
            @endif
        @else
            <div class="text-center p-5">
                <i class="fas fa-truck fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Chưa có đối tác vận chuyển nào</h5>
                <p class="text-muted">Hãy thêm đối tác vận chuyển đầu tiên của bạn!</p>
                <a href="{{ route('admin.doi-tac-van-chuyen.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Thêm đối tác
                </a>
            </div>
        @endif
    </div>

    <style>
        .avatar {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 14px;
        }

        .table td {
            vertical-align: middle;
        }

        .dropdown-menu {
            border: none;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
    </style>
@endsection