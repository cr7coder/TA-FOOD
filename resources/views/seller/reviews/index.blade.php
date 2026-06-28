@extends('seller.layouts.app')

@section('title', 'Quản lý Đánh giá')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h4 fw-bold text-dark mb-0">Phản hồi đánh giá</h2>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            @if($reviews->isEmpty())
                <div class="text-center py-5">
                    <img src="https://cdn-icons-png.flaticon.com/512/7465/7465679.png" alt="no-data" style="width: 80px; opacity: 0.5;">
                    <p class="text-muted mt-3">Chưa có đánh giá nào từ khách hàng.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-muted small">
                            <tr>
                                <th class="px-4 py-3" style="width: 200px;">Khách hàng</th>
                                <th class="py-3" style="width: 150px;">Món ăn</th>
                                <th class="py-3">Đánh giá</th>
                                <th class="px-4 py-3 text-end">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reviews as $review)
                                <tr class="border-bottom">
                                    <td class="px-4 py-4">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; font-weight: bold;">
                                                {{ mb_substr($review->nguoiDung->HoTen ?? 'K', 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark">{{ $review->nguoiDung->HoTen ?? 'Khách lẻ' }}</div>
                                                <div class="text-muted small">{{ $review->created_at->format('d/m/Y H:i') }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-medium text-dark">{{ $review->monAn->TenMonAn ?? 'Món đã xóa' }}</div>
                                    </td>
                                    <td>
                                        <div class="mb-1 text-warning">
                                            {!! $review->diem_danh_gia_html !!}
                                        </div>
                                        <div class="text-dark small" style="max-width: 400px; white-space: normal;">
                                            {{ $review->noi_dung }}
                                        </div>
                                        
                                        @if($review->phan_hoi)
                                            <div class="mt-3 p-3 bg-light rounded-3 border-start border-primary border-4">
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <span class="small fw-bold text-primary"><i class="fas fa-reply me-1"></i> Phản hồi của bạn:</span>
                                                    <span class="text-muted small" style="font-size: 0.75rem;">{{ $review->phan_hoi_at ? $review->phan_hoi_at->format('d/m/Y H:i') : '' }}</span>
                                                </div>
                                                <div class="small text-dark italic">"{{ $review->phan_hoi }}"</div>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4 text-end">
                                        @if(!$review->phan_hoi)
                                            <button type="button" class="btn btn-primary btn-sm rounded-pill px-3" 
                                                    data-bs-toggle="modal" data-bs-target="#replyModal{{ $review->id }}">
                                                Phản hồi
                                            </button>
                                        @else
                                            <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3"
                                                    data-bs-toggle="modal" data-bs-target="#replyModal{{ $review->id }}">
                                                Sửa phản hồi
                                            </button>
                                        @endif
                                    </td>
                                </tr>

                                <!-- Reply Modal -->
                                <div class="modal fade" id="replyModal{{ $review->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 shadow">
                                            <div class="modal-header border-0 pb-0">
                                                <h5 class="modal-title fw-bold">Phản hồi đánh giá</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <form action="{{ route('seller.reviews.reply', $review->id) }}" method="POST">
                                                @csrf
                                                <div class="modal-body py-4">
                                                    <div class="mb-3 p-3 bg-light rounded">
                                                        <div class="text-muted small mb-1">Đánh giá của khách:</div>
                                                        <div class="fw-medium">"{{ $review->noi_dung }}"</div>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">Nội dung phản hồi</label>
                                                        <textarea name="phan_hoi" class="form-control" rows="4" placeholder="Nhập lời cảm ơn hoặc phản hồi của bạn..." required>{{ $review->phan_hoi }}</textarea>
                                                        <div class="form-text mt-2 small text-muted">
                                                            <i class="fas fa-info-circle me-1"></i> Phản hồi chân thành sẽ giúp quán của bạn chuyên nghiệp hơn.
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer border-0 pt-0">
                                                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Hủy</button>
                                                    <button type="submit" class="btn btn-primary rounded-pill px-4">Gửi phản hồi</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-4 border-top">
                    {{ $reviews->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .italic { font-style: italic; }
    .bg-light { background-color: #f8fafc !important; }
    .border-primary { border-color: var(--bs-primary) !important; }
    .rounded-3 { border-radius: 0.75rem !important; }
</style>
@endpush
