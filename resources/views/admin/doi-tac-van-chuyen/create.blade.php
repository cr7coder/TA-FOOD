@extends('admin.admin')

@section('title', 'Thêm đối tác vận chuyển')

@section('page_actions')
    <a href="{{ route('admin.doi-tac-van-chuyen.index') }}" class="btn btn-soft btn-sm">
        <i class="fas fa-arrow-left"></i> Quay lại
    </a>
@endsection

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card-surface">
                <div class="p-3 border-bottom">
                    <h5 class="mb-0">
                        <i class="fas fa-plus-circle me-2 text-primary"></i>
                        Thêm đối tác vận chuyển mới
                    </h5>
                </div>

                <div class="p-4">
                    <form action="{{ route('admin.doi-tac-van-chuyen.store') }}" method="POST" id="createForm">
                        @csrf

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="ten_doi_tac" class="form-label">
                                    <i class="fas fa-building me-1"></i>
                                    Tên đối tác vận chuyển <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control @error('ten_doi_tac') is-invalid @enderror"
                                    id="ten_doi_tac" name="ten_doi_tac" value="{{ old('ten_doi_tac') }}"
                                    placeholder="VD: Giao Hàng Nhanh Express">
                                @error('ten_doi_tac')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="so_dien_thoai" class="form-label">
                                    <i class="fas fa-phone me-1"></i>
                                    Số điện thoại liên hệ <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control @error('so_dien_thoai') is-invalid @enderror"
                                    id="so_dien_thoai" name="so_dien_thoai" value="{{ old('so_dien_thoai') }}"
                                    placeholder="VD: 0123456789">
                                @error('so_dien_thoai')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="email_lien_he" class="form-label">
                                <i class="fas fa-envelope me-1"></i>
                                Email liên hệ <span class="text-danger">*</span>
                            </label>
                            <input type="email" class="form-control @error('email_lien_he') is-invalid @enderror"
                                id="email_lien_he" name="email_lien_he" value="{{ old('email_lien_he') }}"
                                placeholder="VD: contact@ghn.vn">
                            @error('email_lien_he')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="dia_chi_tru_so" class="form-label">
                                <i class="fas fa-map-marker-alt me-1"></i>
                                Địa chỉ trụ sở chính <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control @error('dia_chi_tru_so') is-invalid @enderror" id="dia_chi_tru_so"
                                name="dia_chi_tru_so" rows="3"
                                placeholder="VD: Tầng 5, Tòa nhà ABC, 123 Đường XYZ, Phường DEF, Quận GHI, TP.HCM">{{ old('dia_chi_tru_so') }}</textarea>
                            @error('dia_chi_tru_so')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.doi-tac-van-chuyen.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i>
                                Hủy
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>
                                Lưu đối tác
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <style>
        .form-label {
            font-weight: 600;
            color: var(--text);
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.2rem rgba(139, 92, 246, 0.25);
        }

        .text-danger {
            color: #dc3545 !important;
        }

        .invalid-feedback,
        .error-message {
            display: block;
            width: 100%;
            margin-top: 0.25rem;
            font-size: 0.875em;
            color: #dc3545;
        }

        .form-control.is-invalid {
            border-color: #dc3545;
            padding-right: calc(1.5em + 0.75rem);
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath d='m5.8 3.6 1.4 1.4m0 0 1.4 1.4m-1.4-1.4L5.8 7.4m1.4-1.4L8.6 7.4'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right calc(0.375em + 0.1875rem) center;
            background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
        }

        .form-control.is-invalid:focus {
            border-color: #dc3545;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
        }

        textarea.form-control.is-invalid {
            background-position: top calc(0.375em + 0.1875rem) right calc(0.375em + 0.1875rem);
        }
    </style>

    <!-- Validator JavaScript -->
    <script src="{{ asset('js/doi-tac-van-chuyen-validator.js') }}"></script>
@endsection