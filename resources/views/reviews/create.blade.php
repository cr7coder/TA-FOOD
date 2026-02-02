@extends('layouts.app')

@section('title', 'Đánh giá món ăn')

@section('content')
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-warning text-dark">
                        <h4 class="mb-0">
                            <i class="fas fa-star"></i> Đánh giá món ăn
                        </h4>
                    </div>

                    <div class="card-body">
                        <!-- Thông tin món ăn -->
                        <div class="food-info mb-4 p-3 bg-light rounded">
                            <div class="row align-items-center">
                                <div class="col-md-3">
                                    <img src="{{ $monAn->HinhAnh ? asset('images/' . $monAn->HinhAnh) : asset('images/no-image.png') }}"
                                        alt="{{ $monAn->TenMonAn }}" class="img-fluid rounded"
                                        style="width: 100%; height: 120px; object-fit: cover;">
                                </div>
                                <div class="col-md-9">
                                    <h5>{{ $monAn->TenMonAn }}</h5>
                                    <p class="text-muted mb-1">Đơn hàng #{{ $donHang->MaDonHang }}</p>
                                    <p class="text-muted mb-0">{{ $donHang->created_at->format('d/m/Y H:i') }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Form đánh giá -->
                        <form action="{{ route('reviews.store', ['maDonHang' => $maDonHang, 'maMonAn' => $maMonAn]) }}"
                            method="POST" enctype="multipart/form-data" id="reviewForm">
                            @csrf

                            <!-- Đánh giá sao -->
                            <div class="mb-4">
                                <label class="form-label">
                                    <strong>Chất lượng món ăn <span class="text-danger">*</span></strong>
                                </label>
                                <div class="star-rating">
                                    <input type="hidden" name="diem_danh_gia" id="rating"
                                        value="{{ old('diem_danh_gia') }}">
                                    <div class="stars" id="starRating">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star star" data-rating="{{ $i }}"></i>
                                        @endfor
                                    </div>
                                    <div class="rating-text mt-2">
                                        <span id="ratingText">Chọn số sao để đánh giá</span>
                                    </div>
                                </div>
                                @error('diem_danh_gia')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Nội dung đánh giá -->
                            <div class="mb-4">
                                <label for="noi_dung" class="form-label">
                                    <strong>Nội dung đánh giá <span class="text-danger">*</span></strong>
                                </label>
                                <textarea class="form-control" id="noi_dung" name="noi_dung" rows="4" maxlength="1000"
                                    placeholder="Chia sẻ cảm nhận của bạn về món ăn này...">{{ old('noi_dung') }}</textarea>
                                <div class="form-text">
                                    <span id="charCount">0</span>/1000 ký tự
                                </div>
                                @error('noi_dung')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Upload hình ảnh -->
                            <div class="mb-4">
                                <label for="hinh_anh" class="form-label">
                                    <strong>Hình ảnh minh họa</strong> <span class="text-muted">(Tùy chọn)</span>
                                </label>
                                <input type="file" class="form-control" id="hinh_anh" name="hinh_anh"
                                    accept="image/jpg,image/jpeg,image/png">
                                <div class="form-text">
                                    Định dạng: JPG, JPEG, PNG. Kích thước tối đa: 5MB
                                </div>
                                @error('hinh_anh')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror

                                <!-- Preview hình ảnh -->
                                <div id="imagePreview" class="mt-2" style="display: none;">
                                    <img id="previewImg" src="" alt="Preview" class="img-thumbnail"
                                        style="max-width: 200px; max-height: 200px;">
                                    <button type="button" class="btn btn-sm btn-danger ml-2" id="removeImage">
                                        <i class="fas fa-times"></i> Xóa
                                    </button>
                                </div>
                            </div>

                            <!-- Upload video -->
                            <div class="mb-4">
                                <label for="video" class="form-label">
                                    <strong>Video minh họa</strong> <span class="text-muted">(Tùy chọn)</span>
                                </label>
                                <input type="file" class="form-control" id="video" name="video"
                                    accept="video/mp4,video/mov">
                                <div class="form-text">
                                    Định dạng: MP4, MOV. Kích thước tối đa: 20MB
                                </div>
                                @error('video')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror

                                <!-- Preview video -->
                                <div id="videoPreview" class="mt-2" style="display: none;">
                                    <video id="previewVideo" controls style="max-width: 300px; max-height: 200px;">
                                    </video>
                                    <button type="button" class="btn btn-sm btn-danger ml-2" id="removeVideo">
                                        <i class="fas fa-times"></i> Xóa
                                    </button>
                                </div>
                            </div>

                            <!-- Nút submit -->
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('orders.show', $maDonHang) }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Quay lại
                                </a>
                                <button type="submit" class="btn btn-warning" id="submitBtn">
                                    <i class="fas fa-paper-plane"></i> Gửi đánh giá
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .star-rating {
            text-align: center;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
        }

        .stars i {
            font-size: 2.5rem;
            color: #ddd;
            cursor: pointer;
            transition: all 0.3s ease;
            margin: 0 5px;
        }

        .stars i:hover,
        .stars i.active {
            color: #ffc107;
            transform: scale(1.1);
        }

        .rating-text {
            font-weight: 500;
            color: #666;
        }

        .food-info {
            border: 2px solid #e9ecef;
        }

        #charCount {
            font-weight: 500;
        }

        .form-control:focus {
            border-color: #ffc107;
            box-shadow: 0 0 0 0.2rem rgba(255, 193, 7, 0.25);
        }

        .btn-warning {
            background-color: #ffc107;
            border-color: #ffc107;
            color: #000;
            font-weight: 500;
        }

        .btn-warning:hover {
            background-color: #e0a800;
            border-color: #d39e00;
            color: #000;
        }

        /* Validation error styling */
        .is-invalid {
            border-color: #dc3545 !important;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
        }

        .validation-error {
            font-size: 14px;
            margin-top: 5px;
        }

        /* Star rating error styling */
        .star-rating.error {
            border: 2px solid #dc3545;
            background-color: #f8d7da;
        }
    </style>

    <script src="{{ asset('js/review-validator.js') }}"></script>
@endsection