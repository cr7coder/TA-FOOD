@extends('seller.layout')
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
                @if(session('success'))
                    <div class="alert alert-success py-2 auto-dismiss">{{ session('success') }}</div>
                @endif
                @if(session('warning'))
                    <div class="alert alert-warning py-2 auto-dismiss">{{ session('warning') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger py-2 auto-dismiss">{{ session('error') }}</div>
                @endif


                <form class="row g-2 mb-3">
                    <div class="col-md-3">
                        <input type="text" name="search" class="form-control form-control-sm" placeholder="Tìm tên món..."
                            value="{{ request('search') }}">
                    </div>
                    {{-- <div class="col-md-3">
                        <input type="number" name="MaNhaHang" class="form-control form-control-sm" placeholder="Mã nhà hàng"
                            value="{{ request('MaNhaHang') }}">
                    </div> --}}
                    <div class="col-md-2">
                        <button class="btn btn-sm btn-primary w-100"><i class="fas fa-search"></i> Lọc</button>
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('seller.foods.index') }}" class="btn btn-sm btn-outline-secondary w-100">
                            <i class="fas fa-redo"></i> Reset
                        </a>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-sm table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Mã</th>
                                <th>Tên món</th>
                                <th>Danh mục</th>
                                <th>Giá</th>
                                <th>Trạng thái</th>
                                <th>Ảnh</th>
                                <th width="90">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($monAns as $m)
                                <tr>
                                    <td>{{ $m->MaMonAn }}</td>
                                    <td>{{ $m->TenMonAn }}</td>
                                    <td>{{ $m->DanhMuc }}</td>
                                    <td>{{ number_format($m->Gia, 0, ',', '.') }} đ</td>
                                    <td>
                                        <span class="badge {{ $m->TrangThai === 'Còn bán' ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $m->TrangThai }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($m->HinhAnh)
                                            <img src="{{ asset('images/' . $m->HinhAnh) }}" alt=""
                                                style="width:50px;height:40px;object-fit:cover;border-radius:6px;">
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('seller.foods.edit', $m->MaMonAn) }}"
                                                class="btn btn-sm btn-outline-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('seller.foods.destroy', $m->MaMonAn) }}" method="POST"
                                                onsubmit="return confirm('Bạn có chắc chắn muốn xóa món: {{ $m->TenMonAn }}?');">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-3">Chưa có món ăn nào.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($monAns->hasPages())
                    <div class="d-flex justify-content-end">
                        {{ $monAns->withQueryString()->links('pagination::bootstrap-4') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const alerts = document.querySelectorAll('.auto-dismiss');
            alerts.forEach(function (alert) {
                setTimeout(function () {
                    alert.style.transition = 'opacity 0.5s ease';
                    alert.style.opacity = '0';
                    setTimeout(function () {
                        alert.remove();
                    }, 500);
                }, 2000);
            });
        });
    </script>
@endsection