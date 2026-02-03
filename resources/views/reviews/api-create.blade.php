@extends('layouts.app')

@section('title', 'Đánh giá món ăn')

@section('content')
<style>
    .review-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 24px 16px;
        background: white;
    }

    .page-title {
        font-family: 'Arimo', sans-serif;
        font-size: 36px;
        line-height: 24px;
        color: #0a0a0a;
        text-align: center;
        margin-bottom: 40px;
    }

    .back-button {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        color: #4a5565;
        font-family: 'Arimo', sans-serif;
        font-size: 16px;
        margin-bottom: 24px;
        transition: all 0.2s;
    }

    .back-button:hover {
        color: #ff6900;
        transform: translateX(-5px);
    }

    .back-button i {
        font-size: 20px;
    }

    .loading-container {
        text-align: center;
        padding: 60px 20px;
    }

    .loading-spinner {
        width: 50px;
        height: 50px;
        border: 4px solid #e5e7eb;
        border-top-color: #ff6900;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin: 0 auto 16px;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    .loading-text {
        font-family: 'Arimo', sans-serif;
        font-size: 16px;
        color: #6a7282;
    }

    .review-header {
        background: #fdc700;
        border-radius: 10px;
        padding: 16px 24px;
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 24px;
    }

    .review-header i {
        font-size: 24px;
        color: #101828;
    }

    .review-header-title {
        font-family: 'Arimo', sans-serif;
        font-size: 20px;
        line-height: 28px;
        color: #101828;
        font-weight: 500;
    }

    .review-form-card {
        background: white;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 2px 4px rgba(0, 0, 0, 0.1);
        padding: 24px;
    }

    .food-preview {
        display: flex;
        align-items: center;
        gap: 16px;
        margin-bottom: 32px;
    }

    .food-preview-image {
        width: 96px;
        height: 96px;
        border-radius: 10px;
        object-fit: cover;
    }

    .food-preview-info {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .food-preview-name {
        font-family: 'Arimo', sans-serif;
        font-size: 20px;
        line-height: 28px;
        color: #101828;
        font-weight: 500;
    }

    .food-preview-order {
        font-family: 'Arimo', sans-serif;
        font-size: 14px;
        line-height: 20px;
        color: #4a5565;
    }

    .food-preview-date {
        font-family: 'Arimo', sans-serif;
        font-size: 14px;
        line-height: 20px;
        color: #6a7282;
    }

    .form-group {
        margin-bottom: 24px;
    }

    .form-label {
        font-family: 'Arimo', sans-serif;
        font-size: 16px;
        line-height: 24px;
        color: #101828;
        margin-bottom: 12px;
        display: block;
    }

    .form-label .required {
        color: #fb2c36;
        margin-left: 4px;
    }

    .form-label .optional {
        color: #6a7282;
        font-weight: normal;
        margin-left: 4px;
    }

    .star-rating {
        display: flex;
        gap: 8px;
        margin-bottom: 12px;
    }

    .star-btn {
        background: none;
        border: none;
        cursor: pointer;
        padding: 0;
        font-size: 48px;
        color: #d1d5dc;
        transition: all 0.2s;
        line-height: 1;
    }

    .star-btn:hover,
    .star-btn.active {
        color: #fdc700;
        transform: scale(1.1);
    }

    .star-rating-text {
        font-family: 'Arimo', sans-serif;
        font-size: 14px;
        line-height: 20px;
        color: #6a7282;
    }

    .form-textarea {
        width: 100%;
        padding: 12px 16px;
        border: 0.8px solid #d1d5dc;
        border-radius: 10px;
        font-family: 'Arimo', sans-serif;
        font-size: 16px;
        line-height: 24px;
        color: #0a0a0a;
        resize: vertical;
        min-height: 128px;
        transition: all 0.2s;
    }

    .form-textarea:focus {
        outline: none;
        border-color: #fdc700;
        box-shadow: 0 0 0 3px rgba(253, 199, 0, 0.1);
    }

    .form-textarea.is-invalid {
        border-color: #fb2c36;
    }

    .invalid-feedback {
        display: block;
        color: #fb2c36;
        font-size: 14px;
        margin-top: 4px;
    }

    .char-count {
        font-family: 'Arimo', sans-serif;
        font-size: 14px;
        line-height: 20px;
        color: #6a7282;
        text-align: right;
        margin-top: 4px;
    }

    .image-upload {
        border: 1.6px dashed #d1d5dc;
        border-radius: 10px;
        padding: 32px;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s;
    }

    .image-upload:hover {
        border-color: #fdc700;
        background: rgba(253, 199, 0, 0.05);
    }

    .image-upload-icon {
        font-size: 32px;
        color: #6a7282;
        margin-bottom: 8px;
    }

    .image-upload-text {
        font-family: 'Arimo', sans-serif;
        font-size: 14px;
        line-height: 20px;
        color: #6a7282;
        margin-bottom: 8px;
    }

    .image-upload-hint {
        font-family: 'Arimo', sans-serif;
        font-size: 12px;
        line-height: 16px;
        color: #99a1af;
    }

    .file-input {
        display: none;
    }

    .file-selected {
        font-family: 'Arimo', sans-serif;
        font-size: 12px;
        line-height: 16px;
        color: #6a7282;
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
        border-radius: 10px;
        overflow: hidden;
    }

    .image-preview-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .image-preview-remove {
        position: absolute;
        top: 4px;
        right: 4px;
        background: rgba(0, 0, 0, 0.6);
        border: none;
        border-radius: 50%;
        width: 24px;
        height: 24px;
        color: white;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
    }

    .image-preview-remove:hover {
        background: #fb2c36;
    }

    .form-actions {
        display: flex;
        gap: 16px;
        margin-top: 32px;
    }

    .btn {
        flex: 1;
        padding: 12px 24px;
        border-radius: 26843500px;
        font-family: 'Arimo', sans-serif;
        font-size: 16px;
        line-height: 24px;
        text-align: center;
        text-decoration: none;
        cursor: pointer;
        transition: all 0.2s;
        border: none;
    }

    .btn-cancel {
        background: white;
        border: 0.8px solid #d1d5dc;
        color: #364153;
    }

    .btn-cancel:hover {
        background: #f8f9fa;
        border-color: #99a1af;
    }

    .btn-submit {
        background: #fdc700;
        color: #101828;
        font-weight: 500;
    }

    .btn-submit:hover {
        background: #e5b500;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(253, 199, 0, 0.3);
    }

    .btn-submit:disabled {
        background: #d1d5dc;
        color: #6a7282;
        cursor: not-allowed;
        transform: none;
    }

    .alert {
        padding: 12px 16px;
        border-radius: 10px;
        margin-bottom: 24px;
        font-family: 'Arimo', sans-serif;
        font-size: 14px;
        line-height: 20px;
    }

    .alert-success {
        background: #d1f4e0;
        color: #00a63e;
        border: 1px solid #00a63e;
    }

    .alert-error {
        background: #ffe5e5;
        color: #fb2c36;
        border: 1px solid #fb2c36;
    }
</style>

<div class="review-container">
    <a href="#" onclick="history.back(); return false;" class="back-button">
        <i class="fas fa-arrow-left"></i>
        Quay lại
    </a>

    <h1 class="page-title">Đánh giá món ăn</h1>

    <!-- Loading State -->
    <div id="loadingContainer" class="loading-container" style="display: none;">
        <div class="loading-spinner"></div>
        <div class="loading-text">Đang tải thông tin...</div>
    </div>

    <div id="reviewFormContainer" style="display: none;">
        <div class="review-header">
            <i class="fas fa-star"></i>
            <span class="review-header-title">Đánh giá món ăn</span>
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
                <div class="form-group">
                    <label class="form-label">
                        Chất lượng món ăn
                        <span class="required">*</span>
                    </label>
                    <div class="star-rating" id="starRating">
                        <button type="button" class="star-btn active" data-rating="1">★</button>
                        <button type="button" class="star-btn active" data-rating="2">★</button>
                        <button type="button" class="star-btn active" data-rating="3">★</button>
                        <button type="button" class="star-btn active" data-rating="4">★</button>
                        <button type="button" class="star-btn active" data-rating="5">★</button>
                    </div>
                    <div class="star-rating-text">Chọn số sao để đánh giá: <span id="ratingText">5</span>/5</div>
                </div>

                <!-- Review Content -->
                <div class="form-group">
                    <label class="form-label" for="noiDung">
                        Nội dung đánh giá
                        <span class="required">*</span>
                    </label>
                    <textarea 
                        class="form-textarea" 
                        id="noiDung" 
                        name="content" 
                        maxlength="1000" 
                        placeholder="Ngon quá sước tưởng tượng lại còn rẻ nữa"
                        required></textarea>
                    <div class="invalid-feedback" id="content-error"></div>
                    <div class="char-count">
                        <span id="charCount">0</span>/1000 ký tự
                    </div>
                </div>

                <!-- Image Upload -->
                <div class="form-group">
                    <label class="form-label">
                        Hình ảnh minh họa
                        <span class="optional">(Tùy chọn)</span>
                    </label>
                    <div class="image-upload" id="imageUpload">
                        <div class="image-upload-icon">
                            <i class="fas fa-cloud-upload-alt"></i>
                        </div>
                        <div class="image-upload-text">Chọn tệp</div>
                        <div class="image-upload-hint">Định dạng: JPG, JPEG, PNG. Kích thước tối đa: 5MB</div>
                        <input type="file" 
                               class="file-input" 
                               id="hinhAnh" 
                               name="image" 
                               accept="image/jpeg,image/jpg,image/png">
                    </div>
                    <div class="file-selected" id="fileSelected">Không có tệp nào được chọn</div>
                    <div class="invalid-feedback" id="image-error"></div>
                    <div class="image-preview" id="imagePreview"></div>
                </div>

                <!-- Form Actions -->
                <div class="form-actions">
                    <button type="button" onclick="history.back()" class="btn btn-cancel">Hủy</button>
                    <button type="submit" class="btn btn-submit" id="submitBtn">Gửi đánh giá</button>
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
        
        textarea.addEventListener('input', updateCharCount);
        
        // Image Upload
        const imageUpload = document.getElementById('imageUpload');
        const fileInput = document.getElementById('hinhAnh');
        const fileSelected = document.getElementById('fileSelected');
        const imagePreview = document.getElementById('imagePreview');
        
        imageUpload.addEventListener('click', function() {
            fileInput.click();
        });
        
        fileInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const file = this.files[0];
                fileSelected.textContent = `Đã chọn: ${file.name}`;
                
                // Preview image
                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.innerHTML = `
                        <div class="image-preview-item">
                            <img src="${e.target.result}" class="image-preview-img" alt="Preview">
                            <button type="button" class="image-preview-remove" onclick="removeImage()">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    `;
                };
                reader.readAsDataURL(file);
            }
        });

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

            const imageFile = document.getElementById('hinhAnh').files[0];
            if (imageFile) {
                formData.append('image', imageFile);
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
    });

    function removeImage() {
        const fileInput = document.getElementById('hinhAnh');
        const fileSelected = document.getElementById('fileSelected');
        const imagePreview = document.getElementById('imagePreview');
        
        fileInput.value = '';
        fileSelected.textContent = 'Không có tệp nào được chọn';
        imagePreview.innerHTML = '';
    }

    function displayFormErrors(errors) {
        for (const [field, messages] of Object.entries(errors)) {
            const input = document.getElementById(field === 'noi_dung' ? 'noiDung' : field);
            const errorDiv = document.getElementById(`${field}-error`);
            
            if (input && errorDiv) {
                input.classList.add('is-invalid');
                errorDiv.textContent = messages[0];
                errorDiv.style.display = 'block';
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
    }

    function showError(message) {
        const alert = document.getElementById('errorAlert');
        const messageElement = document.getElementById('errorMessage');
        messageElement.textContent = message;
        alert.style.display = 'block';
        
        // Scroll to top
        window.scrollTo({ top: 0, behavior: 'smooth' });
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
