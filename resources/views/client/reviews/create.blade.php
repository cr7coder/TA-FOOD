@extends('client.layouts.master')

@section('title', 'Đánh giá món ăn')

@section('content')
    <div class="hero_area">
        <div class="bg-box">
            <img src="{{ asset('images/bg2.jpg') }}" alt="">
        </div>
        @include('client.partials._header')
    </div>

<style>
    /* Optimize header area height on PC to prevent excessive scrolling */
    @media (min-width: 992px) {
        .hero_area {
            min-height: 125px !important;
            height: 125px !important;
        }
        .hero_area .bg-box {
            height: 125px !important;
        }
    }

    body {
        background: linear-gradient(135deg, #f0f2f5 0%, #e8ecf1 100%) !important;
        min-height: 100vh;
    }

    .review-container {
        max-width: 1000px;
        margin: 40px auto;
        padding: 40px 32px;
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border-radius: 24px;
        border: 1px solid rgba(255, 255, 255, 0.6);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.05);
        position: relative;
        overflow: hidden;
    }

    .page-title {
        font-family: inherit;
        font-size: 28px;
        font-weight: 800;
        color: #0f172a;
        text-align: center;
        margin-bottom: 30px;
        letter-spacing: -0.5px;
    }

    #detailBackButton:hover {
        background-color: #e69d00 !important;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(255, 190, 51, 0.35) !important;
    }

    @media (max-width: 576px) {
        /* Breadcrumb header */
        .review-container > div:first-child {
            flex-direction: row !important;
            justify-content: space-between !important;
            align-items: center !important;
            flex-wrap: nowrap !important;
            gap: 8px !important;
            padding-bottom: 12px !important;
            margin-bottom: 15px !important;
        }
        .breadcrumb {
            font-size: 11.5px !important;
            margin: 0 !important;
        }
        .breadcrumb-item {
            white-space: nowrap !important;
        }
        #detailBackButton {
            width: auto !important;
            padding: 6px 12px !important;
            font-size: 12px !important;
            flex-shrink: 0 !important;
            margin-left: auto !important;
            gap: 5px !important;
            justify-content: center !important;
        }
    }

    .breadcrumb-item a {
        color: #64748b;
        font-weight: 500;
        text-decoration: none;
        transition: color 0.2s ease;
    }

    .breadcrumb-item a:hover {
        color: #ff6900;
    }

    .loading-container {
        text-align: center;
        padding: 60px 20px;
    }

    .loading-spinner {
        width: 45px;
        height: 45px;
        border: 4.5px solid #f1f5f9;
        border-top-color: #ff6900;
        border-radius: 50%;
        animation: spin 1s cubic-bezier(0.55, 0.15, 0.45, 0.85) infinite;
        margin: 0 auto 16px;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    .loading-text {
        font-family: inherit;
        font-size: 15px;
        font-weight: 500;
        color: #64748b;
    }

    .review-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 28px;
        padding-bottom: 16px;
        border-bottom: 1px solid #f1f5f9;
    }

    .review-header i {
        font-size: 18px;
        color: #ff6900;
        background: rgba(255, 105, 0, 0.08);
        width: 38px;
        height: 38px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
    }

    .review-header-title {
        font-family: inherit;
        font-size: 18px;
        color: #0f172a;
        font-weight: 700;
    }

    .review-form-card {
        background: white;
    }

    .food-preview {
        background: #f8fafc;
        border: 1px solid #f1f5f9;
        border-radius: 16px;
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 20px;
        margin-bottom: 30px;
    }

    .food-preview-image {
        width: 80px;
        height: 80px;
        border-radius: 12px;
        object-fit: cover;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.06);
        border: 1px solid #e2e8f0;
    }

    .food-preview-info {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .food-preview-name {
        font-family: inherit;
        font-size: 18px;
        color: #0f172a;
        font-weight: 700;
    }

    .food-preview-order {
        font-family: inherit;
        font-size: 13.5px;
        color: #64748b;
        font-weight: 600;
    }

    .food-preview-date {
        font-family: inherit;
        font-size: 12px;
        color: #94a3b8;
    }

    .form-group {
        margin-bottom: 24px;
    }

    .form-label {
        font-family: inherit;
        font-size: 14.5px;
        font-weight: 700;
        color: #334155;
        margin-bottom: 8px;
        display: block;
    }

    .form-label .required {
        color: #ef4444;
        margin-left: 3px;
    }

    .form-label .optional {
        color: #94a3b8;
        font-weight: normal;
        margin-left: 4px;
        font-size: 13px;
    }

    .star-rating-box {
        background: #f8fafc;
        border-radius: 16px;
        padding: 24px;
        text-align: center;
        border: 1px solid #f1f5f9;
        margin-bottom: 24px;
    }

    .star-rating {
        display: inline-flex;
        justify-content: center;
        gap: 12px;
        margin-bottom: 8px;
    }

    .star-btn {
        background: none;
        border: none;
        cursor: pointer;
        padding: 0;
        font-size: 40px;
        color: #e2e8f0;
        transition: all 0.25s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        line-height: 1;
    }

    .star-btn:hover {
        transform: scale(1.2);
    }

    .star-btn.active {
        color: #ffb700;
        text-shadow: 0 0 10px rgba(255, 183, 0, 0.3);
    }

    .star-rating-text {
        font-family: inherit;
        font-size: 13.5px;
        color: #64748b;
        font-weight: 600;
    }

    .star-rating-text span {
        color: #ff6900;
        font-size: 16px;
        font-weight: 700;
    }

    .form-textarea {
        width: 100%;
        padding: 16px;
        border: 1.5px solid #e2e8f0;
        border-radius: 14px;
        font-family: inherit;
        font-size: 14.5px;
        line-height: 1.6;
        color: #1e293b;
        resize: vertical;
        min-height: 120px;
        transition: all 0.25s ease;
        background-color: #f8fafc;
        box-sizing: border-box;
    }

    .form-textarea:focus {
        outline: none;
        border-color: #ff6900;
        background-color: #ffffff;
        box-shadow: 0 0 0 4px rgba(255, 105, 0, 0.08);
    }

    .form-textarea.is-invalid {
        border-color: #ef4444;
        background-color: #fffafb;
    }

    .invalid-feedback {
        display: block;
        color: #ef4444;
        font-size: 13px;
        font-weight: 500;
        margin-top: 6px;
    }

    .char-count {
        font-family: inherit;
        font-size: 12px;
        color: #94a3b8;
        text-align: right;
        margin-top: 6px;
        font-weight: 500;
    }

    .image-upload {
        border: 2px dashed #cbd5e1;
        border-radius: 16px;
        padding: 30px;
        text-align: center;
        cursor: pointer;
        background: #f8fafc;
        transition: all 0.25s ease;
    }

    .image-upload:hover {
        border-color: #ff6900;
        background: rgba(255, 105, 0, 0.02);
    }

    .image-upload-icon {
        font-size: 32px;
        color: #ff6900;
        margin-bottom: 8px;
        transition: transform 0.25s ease;
    }

    .image-upload:hover .image-upload-icon {
        transform: translateY(-4px);
    }

    .image-upload-text {
        font-family: inherit;
        font-size: 14px;
        font-weight: 700;
        color: #334155;
        margin-bottom: 4px;
    }

    .image-upload-hint {
        font-family: inherit;
        font-size: 12px;
        color: #64748b;
    }

    .file-input {
        display: none;
    }

    .file-selected {
        font-family: inherit;
        font-size: 12.5px;
        font-weight: 600;
        color: #ff6900;
        margin-top: 8px;
    }

    .image-preview {
        margin-top: 16px;
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }

    .image-preview-item {
        position: relative;
        width: 100px;
        height: 100px;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 10px rgba(0,0,0,0.08);
        border: 1px solid #cbd5e1;
    }

    .image-preview-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .image-preview-remove {
        position: absolute;
        top: 6px;
        right: 6px;
        background: rgba(15, 23, 42, 0.7);
        border: none;
        border-radius: 50%;
        width: 22px;
        height: 22px;
        color: white;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
    }

    .image-preview-remove:hover {
        background: #ef4444;
        transform: scale(1.1);
    }

    .form-actions {
        display: flex;
        gap: 16px;
        margin-top: 35px;
    }

    .btn-action {
        flex: 1;
        padding: 14px 28px;
        border-radius: 14px;
        font-family: inherit;
        font-size: 15px;
        font-weight: 700;
        text-align: center;
        text-decoration: none;
        cursor: pointer;
        transition: all 0.25s ease;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .btn-cancel {
        background: #f1f5f9;
        color: #475569;
    }

    .btn-cancel:hover {
        background: #e2e8f0;
        color: #0f172a;
        transform: translateY(-2px);
    }

    .btn-submit {
        background: linear-gradient(135deg, #ff6900 0%, #ff8a00 100%);
        color: white;
        box-shadow: 0 4px 15px rgba(255, 105, 0, 0.2);
    }

    .btn-submit:hover {
        background: linear-gradient(135deg, #ff8a00 0%, #ff9f00 100%);
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(255, 105, 0, 0.35);
    }

    .btn-submit:disabled {
        background: #cbd5e1;
        color: #94a3b8;
        box-shadow: none;
        cursor: not-allowed;
        transform: none;
    }

    .alert {
        padding: 14px 18px;
        border-radius: 12px;
        margin-bottom: 24px;
        font-family: inherit;
        font-size: 14px.5;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 10px;
        border: none;
    }

    .alert-success {
        background: #ecfdf5;
        color: #059669;
        box-shadow: inset 0 0 0 1px rgba(5, 150, 105, 0.15);
    }

    .alert-error {
        background: #fef2f2;
        color: #dc2626;
        box-shadow: inset 0 0 0 1px rgba(220, 38, 38, 0.15);
    }
</style>

<div class="review-container">
    <!-- Breadcrumb & Back Button -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap" style="gap: 15px; border-bottom: 1px solid #f1f5f9; padding-bottom: 20px; margin-bottom: 25px;">
        <nav aria-label="breadcrumb" class="mb-0">
            <ol class="breadcrumb mb-0" style="background: transparent; padding: 0;">
                <li class="breadcrumb-item"><a href="{{ route('foods.index') }}">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="{{ route('orders.history') }}">Lịch sử đơn hàng</a></li>
                <li class="breadcrumb-item active" aria-current="page" style="color: #0f172a; font-weight: 700;">Đánh giá</li>
            </ol>
        </nav>
        <a href="#" onclick="history.back(); return false;" id="detailBackButton" class="btn btn-warning px-4 py-2 font-weight-bold shadow-sm d-flex align-items-center" 
           style="border-radius: 25px; background-color: #ffbe33; border: none; color: #1e293b; transition: all 0.2s ease; font-size: 13.5px; gap: 8px; font-weight: 700; text-decoration: none; width: fit-content;">
            <i class="fa fa-arrow-left"></i> Quay lại
        </a>
    </div>

    <h1 class="page-title">Đánh giá món ăn</h1>

    <!-- Loading State -->
    <div id="loadingContainer" class="loading-container" style="display: none;">
        <div class="loading-spinner"></div>
        <div class="loading-text">Đang tải thông tin món ăn...</div>
    </div>

    <div id="reviewFormContainer" style="display: none;">
        <div class="review-header">
            <i class="fas fa-star"></i>
            <span class="review-header-title">Viết phản hồi của bạn</span>
        </div>

        <div id="successAlert" class="alert alert-success" style="display: none;">
            <i class="fas fa-check-circle"></i> <span id="successMessage"></span>
        </div>

        <div id="errorAlert" class="alert alert-error" style="display: none;">
            <i class="fas fa-exclamation-circle"></i>
            <div id="errorMessage"></div>
        </div>

        <div class="review-form-card">
            <div class="food-preview" id="foodPreview">
                <!-- Will be populated via JavaScript -->
            </div>

            <form id="reviewForm">
                <input type="hidden" name="rating" id="ratingValue" value="5">

                <!-- Star Rating -->
                <div class="star-rating-box">
                    <label class="form-label text-center" style="font-size: 15px; margin-bottom: 12px;">
                        Chất lượng món ăn <span class="required">*</span>
                    </label>
                    <div class="star-rating" id="starRating">
                        <button type="button" class="star-btn active" data-rating="1">★</button>
                        <button type="button" class="star-btn active" data-rating="2">★</button>
                        <button type="button" class="star-btn active" data-rating="3">★</button>
                        <button type="button" class="star-btn active" data-rating="4">★</button>
                        <button type="button" class="star-btn active" data-rating="5">★</button>
                    </div>
                    <div class="star-rating-text">Bạn đánh giá: <span id="ratingText">5</span> sao</div>
                </div>

                <!-- Review Content -->
                <div class="form-group">
                    <label class="form-label" for="noiDung">
                        Nội dung đánh giá <span class="required">*</span>
                    </label>
                    <textarea 
                        class="form-textarea" 
                        id="noiDung" 
                        name="content" 
                        maxlength="1000" 
                        placeholder="Hãy chia sẻ cảm nhận của bạn về hương vị món ăn, cách đóng gói..."
                        required></textarea>
                    <div class="invalid-feedback" id="content-error"></div>
                    <div class="char-count">
                        <span id="charCount">0</span>/1000 ký tự
                    </div>
                </div>

                <!-- Image Upload -->
                <div class="form-group">
                    <label class="form-label">
                        Hình ảnh minh họa <span class="optional">(Tùy chọn)</span>
                    </label>
                    <div class="image-upload" id="imageUpload">
                        <div class="image-upload-icon">
                            <i class="fas fa-cloud-upload-alt"></i>
                        </div>
                        <div class="image-upload-text">Thêm hình ảnh thực tế</div>
                        <div class="image-upload-hint">Định dạng JPG, PNG, MP4 tối đa 20MB (tối đa 5 tệp)</div>
                        <input type="file" 
                               class="file-input" 
                               id="hinhAnh" 
                               name="images[]" 
                               multiple
                               accept="image/jpeg,image/jpg,image/png,video/mp4,video/quicktime,video/x-msvideo">
                    </div>
                    <div class="file-selected" id="fileSelected" style="display: none;">Không có tệp nào được chọn</div>
                    <div class="invalid-feedback" id="image-error"></div>
                    <div class="image-preview" id="imagePreview"></div>
                </div>

                <!-- Form Actions -->
                <div class="form-actions">
                    <button type="button" onclick="history.back()" class="btn-action btn-cancel">Hủy bỏ</button>
                    <button type="submit" class="btn-action btn-submit" id="submitBtn">
                        <i class="fas fa-paper-plane" style="font-size: 13px;"></i> Gửi đánh giá
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const orderId = {{ $orderId }};
    const foodId = {{ $foodId }};

    // Load order and food info
    async function loadReviewInfo() {
        const loadingContainer = document.getElementById('loadingContainer');
        const reviewFormContainer = document.getElementById('reviewFormContainer');

        loadingContainer.style.display = 'block';

        try {
            const response = await fetch(`/api/v1/orders/${orderId}`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                credentials: 'same-origin'
            });

            const result = await response.json();

            if (response.ok && result.success) {
                const order = result.data;
                const food = order.items.find(item => item.food_id == foodId);

                if (food) {
                    renderFoodPreview(food, order);
                    loadingContainer.style.display = 'none';
                    reviewFormContainer.style.display = 'block';
                } else {
                    throw new Error('Món ăn không có trong đơn hàng này');
                }
            } else {
                throw new Error(result.message || 'Không thể tải thông tin đơn hàng');
            }
        } catch (error) {
            console.error('Error loading review info:', error);
            showError(error.message || 'Đã xảy ra lỗi khi tải thông tin');
        }
    }

    function renderFoodPreview(food, order) {
        const previewContainer = document.getElementById('foodPreview');
        const orderCode = 'ORD' + String(order.id).padStart(3, '0');
        const createdDate = new Date(order.created_at).toLocaleString('vi-VN', {
            year: 'numeric',
            month: '2-digit',
            day: '2-digit',
            hour: '2-digit',
            minute: '2-digit'
        });

        previewContainer.innerHTML = `
            ${food.food_image 
                ? `<img src="${food.food_image}" alt="${food.food_name}" class="food-preview-image">`
                : `<div class="food-preview-image" style="background: linear-gradient(45deg, #ffc107, #ff9800); display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-utensils" style="color: white; font-size: 32px;"></i>
                   </div>`
            }
            
            <div class="food-preview-info">
                <div class="food-preview-name">${food.food_name}</div>
                <div class="food-preview-order">Đơn hàng #${orderCode}</div>
                <div class="food-preview-date">${createdDate}</div>
            </div>
        `;
    }

    // Star Rating Handler
    document.addEventListener('DOMContentLoaded', function() {
        loadReviewInfo();

        const starBtns = document.querySelectorAll('.star-btn');
        const ratingValue = document.getElementById('ratingValue');
        const ratingText = document.getElementById('ratingText');
        
        starBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const rating = parseInt(this.dataset.rating);
                ratingValue.value = rating;
                ratingText.textContent = rating;
                
                starBtns.forEach((star, index) => {
                    if (index < rating) {
                        star.classList.add('active');
                    } else {
                        star.classList.remove('active');
                    }
                });
            });
        });
        
        // Character Counter
        const textarea = document.getElementById('noiDung');
        const charCount = document.getElementById('charCount');
        
        function updateCharCount() {
            charCount.textContent = textarea.value.length;
        }
        
        textarea.addEventListener('input', function() {
            updateCharCount();
            this.classList.remove('is-invalid');
            const errorDiv = document.getElementById('content-error');
            if (errorDiv) {
                errorDiv.textContent = '';
                errorDiv.style.display = 'none';
            }
        });
        
        // Image Upload
        const imageUpload = document.getElementById('imageUpload');
        const fileInput = document.getElementById('hinhAnh');
        const fileSelected = document.getElementById('fileSelected');
        const imagePreview = document.getElementById('imagePreview');
        let accumulatedFiles = new DataTransfer();
        
        imageUpload.addEventListener('click', function() {
            fileInput.click();
        });
        
        fileInput.addEventListener('change', function() {
            if (this.files && this.files.length > 0) {
                let overflow = false;
                Array.from(this.files).forEach(file => {
                    if (accumulatedFiles.items.length < 5) {
                        // Check if file already exists to prevent duplicate addition
                        let exists = false;
                        for (let i = 0; i < accumulatedFiles.items.length; i++) {
                            if (accumulatedFiles.files[i].name === file.name && accumulatedFiles.files[i].size === file.size) {
                                exists = true; break;
                            }
                        }
                        if (!exists) {
                            accumulatedFiles.items.add(file);
                        }
                    } else {
                        overflow = true;
                    }
                });
                
                if (overflow) {
                    const imageError = document.getElementById('image-error');
                    if (imageError) {
                        imageError.textContent = 'Chỉ được tải lên tối đa 5 tệp. Các tệp chọn thêm đã bị bỏ qua.';
                        imageError.style.display = 'block';
                        
                        // Tự động ẩn cảnh báo sau 4 giây
                        setTimeout(() => {
                            if (imageError.textContent.includes('tối đa 5 tệp')) {
                                imageError.textContent = '';
                                imageError.style.display = 'none';
                            }
                        }, 4000);
                    }
                }
            }
            
            // Sync accumulated back to input so it doesn't get cleared by browser's default behavior
            this.files = accumulatedFiles.files;
            renderPreviews();
        });

        function renderPreviews() {
            if (accumulatedFiles.items.length > 0) {
                fileSelected.textContent = `Đã chọn: ${accumulatedFiles.items.length} tệp`;
                fileSelected.style.display = 'block';
                imagePreview.innerHTML = '';
                
                Array.from(accumulatedFiles.files).forEach((file, index) => {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const isVideo = file.type.startsWith('video/');
                        const mediaHtml = isVideo 
                            ? `<video src="${e.target.result}" style="width:100%; height:100%; object-fit:cover;"></video>`
                            : `<img src="${e.target.result}" class="image-preview-img" alt="Preview">`;

                        imagePreview.innerHTML += `
                            <div class="image-preview-item" style="position: relative;">
                                ${mediaHtml}
                                <button type="button" class="image-preview-remove" onclick="removeSpecificFile(${index})">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        `;
                    };
                    reader.readAsDataURL(file);
                });
            } else {
                fileSelected.textContent = 'Không có tệp nào được chọn';
                fileSelected.style.display = 'block';
                imagePreview.innerHTML = '';
            }
        }

        // Form Submit Handler
        document.getElementById('reviewForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const submitBtn = document.getElementById('submitBtn');
            submitBtn.disabled = true;
            submitBtn.textContent = 'Đang gửi...';

            // Clear previous errors
            clearFormErrors();

            const formData = new FormData();
            formData.append('order_id', orderId);
            formData.append('food_id', foodId);
            formData.append('rating', document.getElementById('ratingValue').value);
            formData.append('content', document.getElementById('noiDung').value);

            const imageFiles = document.getElementById('hinhAnh').files;
            if (imageFiles.length > 0) {
                Array.from(imageFiles).forEach(file => {
                    formData.append('images[]', file);
                });
            }

            try {
                const response = await fetch('/api/v1/reviews', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin',
                    body: formData
                });

                const result = await response.json();
                console.log(result);    
                // phản hồi
                if (response.ok && result.success) {
                    showSuccess(result.message || 'Đánh giá món ăn thành công');
                    
                    // Redirect after 2 seconds
                    setTimeout(() => {
                        window.location.href = `/orders/${orderId}`;
                    }, 2000);
                } else {
                    if (result.errors) {
                        displayFormErrors(result.errors);
                    } else {
                        showError(result.message || 'Không thể gửi đánh giá');
                    }
                }
            } catch (error) {
                console.error('Error submitting review:', error);
                showError('Đã xảy ra lỗi khi gửi đánh giá');
            } finally {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Gửi đánh giá';
            }
        });

        window.removeSpecificFile = function(index) {
            const fileInput = document.getElementById('hinhAnh');
            const newDt = new DataTransfer();
            const { files } = accumulatedFiles;
            
            for (let i = 0; i < files.length; i++) {
                if (i !== index) {
                    newDt.items.add(files[i]);
                }
            }
            
            accumulatedFiles = newDt;
            fileInput.files = accumulatedFiles.files;
            
            // Hide the error alert if we have <= 5 files
            if (accumulatedFiles.files.length <= 5) {
                const imageError = document.getElementById('image-error');
                if (imageError) {
                    imageError.textContent = '';
                    imageError.style.display = 'none';
                }
            }
            
            renderPreviews();
        };
    });

    function displayFormErrors(errors) {
        let hasAlerted = false;
        
        for (const [field, messages] of Object.entries(errors)) {
            let inputId = field;
            let errorId = `${field}-error`;
            
            // Map API field names to HTML Element IDs
            if (field === 'content' || field === 'noi_dung') {
                inputId = 'noiDung';
                errorId = 'content-error';
            } else if (field === 'rating') {
                inputId = 'ratingValue';
                errorId = null;
            } else if (field === 'images' || field.startsWith('images.')) {
                inputId = 'hinhAnh';
                errorId = 'image-error';
            }
            
            const input = document.getElementById(inputId);
            const errorDiv = errorId ? document.getElementById(errorId) : null;
            
            if (input) {
                input.classList.add('is-invalid');
            }
            
            if (errorDiv) {
                errorDiv.textContent = messages[0];
                errorDiv.style.display = 'block';
            } else {
                // If there's no specific error div, show it in the general error alert box
                if (!hasAlerted) {
                    showError(messages[0]);
                    hasAlerted = true;
                }
            }
        }
    }

    function clearFormErrors() {
        const inputs = document.querySelectorAll('.is-invalid');
        inputs.forEach(input => input.classList.remove('is-invalid'));
        
        const errors = document.querySelectorAll('.invalid-feedback');
        errors.forEach(error => {
            error.textContent = '';
            error.style.display = 'none';
        });

        // Ẩn luôn thông báo lỗi chung ở đầu trang nếu có
        const errorAlert = document.getElementById('errorAlert');
        const errorMessage = document.getElementById('errorMessage');
        if (errorAlert) {
            errorAlert.style.display = 'none';
        }
        if (errorMessage) {
            errorMessage.textContent = '';
        }
    }

    let errorTimeout;
    function showError(message) {
        const alert = document.getElementById('errorAlert');
        const messageElement = document.getElementById('errorMessage');
        messageElement.textContent = message;
        alert.style.display = 'block';
        
        // Scroll to top
        window.scrollTo({ top: 0, behavior: 'smooth' });

        // Tự động ẩn thông báo sau 4 giây
        clearTimeout(errorTimeout);
        errorTimeout = setTimeout(() => {
            alert.style.display = 'none';
            messageElement.textContent = '';
        }, 4000);
    }

    function showSuccess(message) {
        const alert = document.getElementById('successAlert');
        const messageElement = document.getElementById('successMessage');
        messageElement.textContent = message;
        alert.style.display = 'block';
        
        // Scroll to top
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
</script>
@endsection
