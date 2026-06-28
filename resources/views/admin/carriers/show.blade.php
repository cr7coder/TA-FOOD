@extends('admin.layouts.app')

@section('title', 'Chi tiết Đối tác')

@section('content')
    <style>
        /* Custom styles to match the provided image */
        .detail-banner {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            border-radius: 15px;
            padding: 2rem;
            color: white;
            margin-bottom: 2rem;
            box-shadow: 0 10px 25px rgba(79, 70, 229, 0.15);
        }
        
        .banner-title {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .banner-subtitle {
            font-size: 1rem;
            opacity: 0.9;
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .info-card-mini {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(5px);
            border-radius: 10px;
            padding: 1rem;
            color: white;
            height: 100%;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .mini-label {
            font-size: 0.8rem;
            opacity: 0.8;
            margin-bottom: 0.3rem;
        }
        
        .mini-value {
            font-size: 1.2rem;
            font-weight: 700;
        }
        
        .detail-card {
            background: white;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            padding: 1.5rem;
            height: 100%;
            box-shadow: 0 2px 10px rgba(0,0,0,0.02);
            transition: all 0.3s ease;
        }
        .detail-card:hover {
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            transform: translateY(-2px);
        }
        
        .card-label {
            font-size: 0.85rem;
            color: #6b7280;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .card-value {
            font-size: 1.1rem;
            font-weight: 600;
            color: #1f2937;
        }
        
        .btn-edit {
            background-color: #2563eb;
            color: white;
            border-radius: 10px;
            padding: 0.6rem 1.5rem;
            font-weight: 600;
        }
        .btn-edit:hover {
            background-color: #1d4ed8;
            color: white;
        }
        
        .btn-delete {
            background-color: #ef4444;
            color: white;
            border-radius: 10px;
            padding: 0.6rem 1.5rem;
            font-weight: 600;
        }
        .btn-delete:hover {
            background-color: #dc2626;
            color: white;
        }
        
        .btn-outline-secondary {
            border-color: #d1d5db;
            color: #4b5563;
            border-radius: 10px;
            padding: 0.6rem 1.5rem;
            font-weight: 600;
            background: white;
        }
        .btn-outline-secondary:hover {
            background-color: #f3f4f6;
            color: #1f2937;
        }
    </style>

    <div class="container-fluid py-4">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="d-flex align-items-center">
                <div class="bg-primary text-white p-2 rounded-3 me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                    <i class="fas fa-eye fs-5"></i>
                </div>
                <h4 class="fw-bold mb-0 text-dark">Chi tiết Đối tác</h4>
            </div>
            <a href="{{ route('admin.doi-tac-van-chuyen.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i> Quay lại
            </a>
        </div>

        <!-- Banner Card -->
        <div class="detail-banner">
            <div class="row align-items-center">
                <div class="col-md-7 mb-3 mb-md-0">
                    <div class="d-flex align-items-center gap-3">
                        <!-- Mock Logo (Truck Emoji) -->
                        <div class="bg-white rounded-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; font-size: 2rem;">
                            🚚
                        </div>
                        <div>
                            <div class="banner-title">{{ $doiTacVanChuyen->ten_doi_tac }}</div>
                            <div class="banner-subtitle">
                                <span><i class="fas fa-star text-warning"></i> 4.5</span>
                                <span>•</span>
                                <span>15,420 đơn hàng</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="row g-2">
                        <div class="col-4">
                            <div class="info-card-mini">
                                <div class="mini-label">Phí cơ bản</div>
                                <div class="mini-value">{{ number_format($doiTacVanChuyen->phi_van_chuyen, 0, ',', '.') }} đ</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="info-card-mini">
                                <div class="mini-label">Phí/km</div>
                                <div class="mini-value">{{ number_format($doiTacVanChuyen->phi_km ?? 0, 0, ',', '.') }} đ</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="info-card-mini">
                                <div class="mini-label">Trạng thái</div>
                                <div class="mini-value">{{ $doiTacVanChuyen->trang_thai ?? 'Hoạt động' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detail Grid -->
        <div class="row g-4 mb-4">
            <!-- Người liên hệ -->
            <div class="col-md-6">
                <div class="detail-card">
                    <div class="card-label">
                        <i class="fas fa-user"></i> Người liên hệ
                    </div>
                    <div class="card-value">
                        {{ $doiTacVanChuyen->nguoi_lien_he ?? 'Chưa cập nhật' }}
                    </div>
                </div>
            </div>

            <!-- Số điện thoại -->
            <div class="col-md-6">
                <div class="detail-card">
                    <div class="card-label">
                        <i class="fas fa-phone"></i> Số điện thoại
                    </div>
                    <div class="card-value">
                        {{ $doiTacVanChuyen->so_dien_thoai }}
                    </div>
                </div>
            </div>

            <!-- Email -->
            <div class="col-md-6">
                <div class="detail-card">
                    <div class="card-label">
                        <i class="fas fa-envelope"></i> Email
                    </div>
                    <div class="card-value">
                        {{ $doiTacVanChuyen->email_lien_he }}
                    </div>
                </div>
            </div>

            <!-- Địa chỉ (Thay thế cho Khu vực phủ sóng vì có trong DB) -->
            <div class="col-md-6">
                <div class="detail-card">
                    <div class="card-label">
                        <i class="fas fa-map-marker-alt"></i> Địa chỉ trụ sở chính
                    </div>
                    <div class="card-value">
                        {{ $doiTacVanChuyen->dia_chi_tru_so }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="d-flex justify-content-end gap-3 mt-5">
            <a href="{{ route('admin.doi-tac-van-chuyen.edit', $doiTacVanChuyen->id) }}" class="btn btn-edit">
                <i class="fas fa-edit me-2"></i> Chỉnh sửa
            </a>
            <button class="btn btn-delete btn-delete-action" data-id="{{ $doiTacVanChuyen->id }}" data-name="{{ $doiTacVanChuyen->ten_doi_tac }}">
                <i class="fas fa-trash me-2"></i> Xóa
            </button>
        </div>
    </div>

    <!-- Script xử lý xóa nếu cần -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const deleteBtn = document.querySelector('.btn-delete-action');
            if (deleteBtn) {
                deleteBtn.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    const name = this.getAttribute('data-name');
                    
                    if (confirm(`Bạn có chắc chắn muốn xóa đối tác "${name}" không?`)) {
                        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                        
                        fetch(`/api/v1/admin/doi-tac-van-chuyen/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': token,
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => response.json())
                        .then(res => {
                            if (res.success) {
                                alert('Xóa đối tác thành công!');
                                window.location.href = '/admin/doi-tac-van-chuyen';
                            } else {
                                alert(`Lỗi: ${res.message || 'Không thể xóa.'}`);
                            }
                        })
                        .catch(err => {
                            console.error('Error:', err);
                            alert('Có lỗi xảy ra khi kết nối server.');
                        });
                    }
                });
            }
        });
    </script>
@endsection