@extends('admin.layouts.app')

@section('title', 'Chỉnh sửa Đối Tác')

@section('content')
    <style>
        /* Custom styles to match the provided image */
        .form-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            border: 1px solid #e5e7eb;
            padding: 1.5rem 2rem; /* Giảm padding trên dưới để bớt cao */
        }
        
        .form-label {
            font-weight: 600;
            color: #4b5563;
            margin-bottom: 0.3rem; /* Giảm khoảng cách để form gọn hơn */
            font-size: 0.9rem;
        }
        
        .form-control {
            border-radius: 10px;
            border: 1px solid #d1d5db;
            padding: 0.5rem 1rem; /* Giảm padding input để bớt cao */
            font-size: 0.95rem;
            background-color: #f9fafb;
        }
        
        .form-control:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
            background-color: white;
        }
        
        .input-group-text {
            border-radius: 10px 0 0 10px;
            border: 1px solid #d1d5db;
            background-color: #f9fafb;
            color: #9ca3af;
        }
        
        .input-group .form-control {
            border-radius: 0 10px 10px 0;
        }
        
        .btn-primary {
            background-color: #2563eb;
            border-color: #2563eb;
            border-radius: 10px;
            padding: 0.5rem 1.5rem;
            font-weight: 600;
        }
        .btn-primary:hover {
            background-color: #1d4ed8;
            border-color: #1d4ed8;
        }
        
        .btn-outline-secondary {
            border-color: #d1d5db;
            color: #4b5563;
            border-radius: 10px;
            padding: 0.5rem 1.5rem;
            font-weight: 600;
            background: white;
        }
        .btn-outline-secondary:hover {
            background-color: #f3f4f6;
            color: #1f2937;
            border-color: #d1d5db;
        }
        
        .text-danger {
            color: #ef4444 !important;
        }
    </style>

    <div class="container-fluid py-3"> <!-- Giảm padding container -->
        <div class="row justify-content-center">
            <div class="col-lg-10"> <!-- Tăng chiều rộng lên col-lg-10 như cũ -->
                <!-- Header -->
                <div class="d-flex justify-content-between align-items-center mb-3"> <!-- Giảm mb -->
                    <div class="d-flex align-items-center">
                        <div class="bg-primary text-white p-2 rounded-3 me-3 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                            <i class="fas fa-edit fs-5"></i>
                        </div>
                        <h4 class="fw-bold mb-0 text-dark">Chỉnh sửa Đối Tác</h4>
                    </div>
                    <a href="{{ route('admin.doi-tac-van-chuyen.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i> Quay lại
                    </a>
                </div>

                <!-- Form Card -->
                <div class="form-card">
                    <form action="{{ route('admin.doi-tac-van-chuyen.update', $doiTacVanChuyen) }}" method="POST"
                        id="editForm" data-id="{{ $doiTacVanChuyen->id }}" novalidate>
                        @csrf
                        @method('PUT')

                        <div class="row mb-3"> <!-- Giảm mb từ 4 xuống 3 -->
                            <!-- Tên đối tác -->
                            <div class="col-md-6 mb-3 mb-md-0">
                                <label for="ten_doi_tac" class="form-label">Tên đối tác <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-building"></i></span>
                                    <input type="text" class="form-control @error('ten_doi_tac') is-invalid @enderror"
                                        id="ten_doi_tac" name="ten_doi_tac" value="{{ old('ten_doi_tac', $doiTacVanChuyen->ten_doi_tac) }}"
                                        placeholder="Nhập tên đối tác" required>
                                </div>
                                @error('ten_doi_tac')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Người liên hệ -->
                            <div class="col-md-6">
                                <label for="nguoi_lien_he" class="form-label">Người liên hệ <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    <input type="text" class="form-control @error('nguoi_lien_he') is-invalid @enderror"
                                        id="nguoi_lien_he" name="nguoi_lien_he" value="{{ old('nguoi_lien_he', $doiTacVanChuyen->nguoi_lien_he) }}"
                                        placeholder="Nhập tên người liên hệ" required>
                                </div>
                                @error('nguoi_lien_he')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <!-- Số điện thoại -->
                            <div class="col-md-6 mb-3 mb-md-0">
                                <label for="so_dien_thoai" class="form-label">Số điện thoại <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                    <input type="text" class="form-control @error('so_dien_thoai') is-invalid @enderror"
                                        id="so_dien_thoai" name="so_dien_thoai" value="{{ old('so_dien_thoai', $doiTacVanChuyen->so_dien_thoai) }}"
                                        placeholder="0901234567" required>
                                </div>
                                @error('so_dien_thoai')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div class="col-md-6">
                                <label for="email_lien_he" class="form-label">Email <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                    <input type="email" class="form-control @error('email_lien_he') is-invalid @enderror"
                                        id="email_lien_he" name="email_lien_he" value="{{ old('email_lien_he', $doiTacVanChuyen->email_lien_he) }}"
                                        placeholder="contact@example.com" required>
                                </div>
                                @error('email_lien_he')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <!-- Phí cơ bản -->
                            <div class="col-md-6 mb-3 mb-md-0">
                                <label for="phi_van_chuyen" class="form-label">Phí cơ bản (VND) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-dollar-sign"></i></span>
                                    <input type="number" class="form-control @error('phi_van_chuyen') is-invalid @enderror"
                                        id="phi_van_chuyen" name="phi_van_chuyen" value="{{ old('phi_van_chuyen', $doiTacVanChuyen->phi_van_chuyen) }}"
                                        min="0" step="1000" placeholder="15000" required>
                                </div>
                                @error('phi_van_chuyen')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Phí theo km -->
                            <div class="col-md-6">
                                <label for="phi_km" class="form-label">Phí theo km (VND) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-road"></i></span>
                                    <input type="number" class="form-control @error('phi_km') is-invalid @enderror"
                                        id="phi_km" name="phi_km" value="{{ old('phi_km', $doiTacVanChuyen->phi_km ?? 0) }}"
                                        min="0" step="500" placeholder="3000" required>
                                </div>
                                @error('phi_km')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Địa chỉ -->
                        <div class="mb-3">
                            <label for="dia_chi_tru_so" class="form-label">Địa chỉ trụ sở chính <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('dia_chi_tru_so') is-invalid @enderror" id="dia_chi_tru_so"
                                name="dia_chi_tru_so" rows="2"
                                placeholder="Nhập địa chỉ trụ sở chính" required>{{ old('dia_chi_tru_so', $doiTacVanChuyen->dia_chi_tru_so) }}</textarea>
                            @error('dia_chi_tru_so')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Footer Actions -->
                        <div class="d-flex justify-content-end gap-3 mt-4"> <!-- Giảm mt -->
                            <a href="{{ route('admin.doi-tac-van-chuyen.index') }}" class="btn btn-outline-secondary">
                                Hủy
                            </a>
                            <button type="submit" class="btn btn-primary text-white">
                                <i class="fas fa-check me-2"></i> Lưu thay đổi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Validator JavaScript -->
    <script src="{{ asset('js/doi-tac-van-chuyen-validator.js') }}?v={{ time() }}"></script>
@endsection