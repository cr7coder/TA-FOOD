/**
 * Review Form Validation JavaScript
 * Handles validation for food review form
 */

class ReviewFormValidator {
    constructor() {
        this.form = document.getElementById('reviewForm');
        this.stars = document.querySelectorAll('.star');
        this.ratingInput = document.getElementById('rating');
        this.ratingText = document.getElementById('ratingText');
        this.textarea = document.getElementById('noi_dung');
        this.charCount = document.getElementById('charCount');
        this.imageInput = document.getElementById('hinh_anh');
        this.videoInput = document.getElementById('video');
        this.submitBtn = document.getElementById('submitBtn');
        this.imagePreview = document.getElementById('imagePreview');
        this.videoPreview = document.getElementById('videoPreview');
        this.previewImg = document.getElementById('previewImg');
        this.previewVideo = document.getElementById('previewVideo');
        this.removeImageBtn = document.getElementById('removeImage');
        this.removeVideoBtn = document.getElementById('removeVideo');

        this.ratingTexts = {
            1: '1 sao - Rất không hài lòng',
            2: '2 sao - Không hài lòng',
            3: '3 sao - Bình thường',
            4: '4 sao - Hài lòng',
            5: '5 sao - Rất hài lòng'
        };

        this.bannedWords = [
            'tục tĩu', 'ngu', 'chết', 'đồ chó', 'súc vật', 'khốn nạn',
            'đàn bà', 'con điên', 'thằng khốn', 'đồ ngu', 'óc chó',
            'đụ', 'fuck', 'shit', 'damn', 'bitch', 'ass', 'hell'
        ];

        this.init();
    }

    init() {
        this.initStarRating();
        this.initCharacterCount();
        this.initFileUpload();
        this.initFormValidation();
        this.setInitialRating();
    }

    // Initialize star rating functionality
    initStarRating() {
        this.stars.forEach(star => {
            star.addEventListener('click', (e) => {
                const rating = parseInt(e.target.dataset.rating);
                this.ratingInput.value = rating;
                this.updateStars(rating);
                this.ratingText.textContent = this.ratingTexts[rating];
                this.clearError('diem_danh_gia');
            });

            star.addEventListener('mouseover', (e) => {
                const rating = parseInt(e.target.dataset.rating);
                this.highlightStars(rating);
            });
        });

        document.querySelector('.stars').addEventListener('mouseleave', () => {
            this.updateStars(parseInt(this.ratingInput.value) || 0);
        });
    }

    // Set initial rating if exists
    setInitialRating() {
        if (this.ratingInput.value) {
            this.updateStars(parseInt(this.ratingInput.value));
        }
    }

    // Update star display
    updateStars(rating) {
        this.stars.forEach((star, index) => {
            if (index < rating) {
                star.classList.add('active');
            } else {
                star.classList.remove('active');
            }
        });
    }

    // Highlight stars on hover
    highlightStars(rating) {
        this.stars.forEach((star, index) => {
            if (index < rating) {
                star.style.color = '#ffc107';
            } else {
                star.style.color = '#ddd';
            }
        });
    }

    // Initialize character count
    initCharacterCount() {
        this.textarea.addEventListener('input', () => {
            this.updateCharCount();
            this.clearError('noi_dung');
        });
        this.updateCharCount(); // Initial count
    }

    // Update character count display
    updateCharCount() {
        const count = this.textarea.value.length;
        this.charCount.textContent = count;

        if (count > 900) {
            this.charCount.style.color = '#dc3545';
        } else if (count > 700) {
            this.charCount.style.color = '#ffc107';
        } else {
            this.charCount.style.color = '#6c757d';
        }
    }

    // Initialize file upload functionality
    initFileUpload() {
        // Image upload
        this.imageInput.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (file) {
                if (this.validateImageFile(file)) {
                    this.previewImage(file);
                } else {
                    e.target.value = '';
                }
            }
        });

        this.removeImageBtn.addEventListener('click', () => {
            this.clearImagePreview();
        });

        // Video upload
        this.videoInput.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (file) {
                if (this.validateVideoFile(file)) {
                    this.previewVideo(file);
                } else {
                    e.target.value = '';
                }
            }
        });

        this.removeVideoBtn.addEventListener('click', () => {
            this.clearVideoPreview();
        });
    }

    // Validate image file
    validateImageFile(file) {
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
        const maxSize = 5 * 1024 * 1024; // 5MB

        // 3E.5: Check image format
        if (!allowedTypes.includes(file.type)) {
            this.showError('hinh_anh', 'Hình ảnh chỉ hỗ trợ jpg, jpeg, png');
            return false;
        }

        // 3E.7: Check image size
        if (file.size > maxSize) {
            this.showError('hinh_anh', 'Hình ảnh không được vượt quá 5MB');
            return false;
        }

        this.clearError('hinh_anh');
        return true;
    }

    // Validate video file
    validateVideoFile(file) {
        const allowedTypes = ['video/mp4', 'video/mov'];
        const maxSize = 20 * 1024 * 1024; // 20MB

        // 3E.6: Check video format
        if (!allowedTypes.includes(file.type)) {
            this.showError('video', 'Video chỉ hỗ trợ mp4, mov');
            return false;
        }

        // 3E.8: Check video size
        if (file.size > maxSize) {
            this.showError('video', 'Video không được vượt quá 20MB');
            return false;
        }

        this.clearError('video');
        return true;
    }

    // Preview image
    previewImage(file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            this.previewImg.src = e.target.result;
            this.imagePreview.style.display = 'block';
        };
        reader.readAsDataURL(file);
    }

    // Preview video
    previewVideo(file) {
        const url = URL.createObjectURL(file);
        this.previewVideo.src = url;
        this.videoPreview.style.display = 'block';
    }

    // Clear image preview
    clearImagePreview() {
        this.imageInput.value = '';
        this.imagePreview.style.display = 'none';
        this.previewImg.src = '';
        this.clearError('hinh_anh');
    }

    // Clear video preview
    clearVideoPreview() {
        this.videoInput.value = '';
        this.videoPreview.style.display = 'none';
        this.previewVideo.src = '';
        this.clearError('video');
    }

    // Check for banned words
    containsBannedWords(text) {
        const lowerText = text.toLowerCase();
        return this.bannedWords.some(word => lowerText.includes(word.toLowerCase()));
    }

    // Validate content field
    validateContent() {
        const content = this.textarea.value.trim();

        // 3E.2: Check if content is empty
        if (!content) {
            this.showError('noi_dung', 'Vui lòng nhập nội dung đánh giá');
            return false;
        }

        // 3E.3: Check content length
        if (content.length > 1000) {
            this.showError('noi_dung', 'Nội dung đánh giá không quá 1000 ký tự');
            return false;
        }

        // 3E.4: Check for banned words
        if (this.containsBannedWords(content)) {
            this.showError('noi_dung', 'Nội dung đánh giá chứa từ ngữ không phù hợp');
            return false;
        }

        return true;
    }

    // Validate rating field
    validateRating() {
        // 3E.1: Validate rating
        if (!this.ratingInput.value || this.ratingInput.value < 1) {
            this.showError('rating', 'Vui lòng chọn số sao để đánh giá món ăn');
            return false;
        }
        return true;
    }

    // Show error message
    showError(fieldName, message) {
        this.clearError(fieldName);
        const field = document.getElementById(fieldName) || document.querySelector(`[name="${fieldName}"]`);
        if (field) {
            const errorDiv = document.createElement('div');
            errorDiv.className = 'text-danger mt-1 validation-error';
            errorDiv.textContent = message;
            field.parentNode.appendChild(errorDiv);

            // Add error styling to field
            field.classList.add('is-invalid');
        }
    }

    // Clear error message
    clearError(fieldName) {
        const field = document.getElementById(fieldName) || document.querySelector(`[name="${fieldName}"]`);
        if (field) {
            // Remove error styling
            field.classList.remove('is-invalid');

            // Remove error messages
            const errorMessages = field.parentNode.querySelectorAll('.validation-error');
            errorMessages.forEach(msg => msg.remove());
        }
    }

    // Clear all errors
    clearAllErrors() {
        document.querySelectorAll('.validation-error').forEach(error => error.remove());
        document.querySelectorAll('.is-invalid').forEach(field => field.classList.remove('is-invalid'));
    }

    // Initialize form validation
    initFormValidation() {
        this.form.addEventListener('submit', (e) => {
            if (!this.validateForm()) {
                e.preventDefault();
                // Scroll to first error
                const firstError = document.querySelector('.validation-error');
                if (firstError) {
                    firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
                return false;
            }

            // Disable submit button to prevent double submission
            this.submitBtn.disabled = true;
            this.submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang gửi...';
        });
    }

    // Validate entire form
    validateForm() {
        let valid = true;

        // Clear all previous errors
        this.clearAllErrors();

        // Validate rating
        if (!this.validateRating()) {
            valid = false;
        }

        // Validate content
        if (!this.validateContent()) {
            valid = false;
        }

        // Validate image file (if selected)
        if (this.imageInput.files.length > 0) {
            const imageFile = this.imageInput.files[0];
            if (!this.validateImageFile(imageFile)) {
                valid = false;
            }
        }

        // Validate video file (if selected)
        if (this.videoInput.files.length > 0) {
            const videoFile = this.videoInput.files[0];
            if (!this.validateVideoFile(videoFile)) {
                valid = false;
            }
        }

        return valid;
    }
}

// Initialize validator when DOM is loaded
document.addEventListener('DOMContentLoaded', function () {
    new ReviewFormValidator();
});