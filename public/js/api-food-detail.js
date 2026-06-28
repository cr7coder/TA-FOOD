// API Configuration for Food Detail
const API_BASE_URL = '/api/v1';

// Get food ID from URL
function getFoodIdFromUrl() {
    const pathParts = window.location.pathname.split('/');
    return pathParts[pathParts.length - 1];
}

// Load food detail from API
async function loadFoodDetail() {
    const foodId = getFoodIdFromUrl();
    
    try {
        showDetailLoading();
        
        const response = await fetch(`${API_BASE_URL}/foods/${foodId}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        });

        const result = await response.json();

        if (result.success) {
            renderFoodDetail(result.data.food);
            renderRelatedFoods(result.data.related_foods);
            hideDetailLoading();
        } else {
            showDetailError(result.message);
        }
    } catch (error) {
        console.error('Error loading food detail:', error);
        showDetailError('Có lỗi xảy ra khi tải thông tin món ăn');
    }
}

// Render food detail
function renderFoodDetail(food) {
    // Update food name
    const nameElement = document.querySelector('.food-detail-name');
    if (nameElement) {
        nameElement.textContent = food.TenMonAn;
    }

    // Update food image
    const imageElement = document.querySelector('.food-detail-image img');
    if (imageElement) {
        imageElement.src = food.hinh_anh_url || food.hinhAnhUrl || (food.HinhAnh ? (food.HinhAnh.startsWith('http') ? food.HinhAnh : `/images/${food.HinhAnh}`) : '/images/no-image.png');
        imageElement.alt = food.TenMonAn;
    }

    // Update price
    const priceElement = document.querySelector('.food-detail-price');
    if (priceElement) {
        priceElement.textContent = formatPrice(food.Gia) + ' đ';
    }

    // Update description
    const descElement = document.querySelector('.food-detail-description');
    if (descElement) {
        descElement.textContent = food.MoTa || 'Chưa có mô tả';
    }

    // Update category
    const categoryElement = document.querySelector('.food-detail-category');
    if (categoryElement) {
        categoryElement.textContent = food.DanhMuc;
    }

    // Update status
    const statusElement = document.querySelector('.food-detail-status');
    if (statusElement) {
        statusElement.textContent = food.TrangThai;
        statusElement.className = `badge ${food.TrangThai === 'Còn bán' ? 'bg-success' : 'bg-danger'}`;
    }

    // Update restaurant info
    if (food.nha_hang) {
        const restaurantNameElement = document.querySelector('.restaurant-name');
        if (restaurantNameElement) {
            restaurantNameElement.textContent = food.nha_hang.TenNhaHang;
        }

        const restaurantAddressElement = document.querySelector('.restaurant-address');
        if (restaurantAddressElement) {
            restaurantAddressElement.textContent = food.nha_hang.DiaChi;
        }
    }

    // Update rating
    const ratingElement = document.querySelector('.food-rating');
    if (ratingElement) {
        ratingElement.innerHTML = renderStars(food.diem_trung_binh || 0);
    }

    // Update reviews count
    const reviewsCountElement = document.querySelector('.reviews-count');
    if (reviewsCountElement) {
        reviewsCountElement.textContent = `(${food.tong_binh_luan || 0} đánh giá)`;
    }

    // Update reviews list
    renderReviews(food.binh_luans || []);

    // Update add to cart button
    const addToCartBtn = document.querySelector('.add-to-cart-detail');
    if (addToCartBtn) {
        addToCartBtn.dataset.id = food.MaMonAn;
        addToCartBtn.dataset.name = food.TenMonAn;
        addToCartBtn.dataset.price = food.Gia;
        addToCartBtn.dataset.image = food.hinh_anh_url || food.hinhAnhUrl || (food.HinhAnh ? (food.HinhAnh.startsWith('http') ? food.HinhAnh : `/images/${food.HinhAnh}`) : '/images/no-image.png');
        
        if (food.TrangThai !== 'Còn bán') {
            addToCartBtn.disabled = true;
            addToCartBtn.textContent = 'Ngừng bán';
        }
    }
}

// Render related foods
function renderRelatedFoods(relatedFoods) {
    const container = document.querySelector('.related-foods-container');
    if (!container) return;

    if (relatedFoods.length === 0) {
        container.innerHTML = '<p class="text-center text-muted">Không có món ăn liên quan</p>';
        return;
    }

    container.innerHTML = relatedFoods.map(food => `
        <div class="col-sm-6 col-lg-3">
            <div class="box">
                <div>
                    <div class="img-box">
                        <img src="${food.hinh_anh_url || food.hinhAnhUrl || (food.HinhAnh ? (food.HinhAnh.startsWith('http') ? food.HinhAnh : '/images/' + food.HinhAnh) : '/images/no-image.png')}" 
                             alt="${food.TenMonAn}">
                    </div>
                    <div class="detail-box">
                        <h5>
                            <a href="/food/${food.MaMonAn}" class="text-decoration-none text-light">
                                ${food.TenMonAn}
                            </a>
                        </h5>
                        ${food.MoTa ? `<p>${truncate(food.MoTa, 50)}</p>` : ''}
                        <div class="rating mb-2">
                            ${renderStars(food.diem_trung_binh || 0)}
                            <small class="text-muted ms-1">(${food.tong_binh_luan || 0})</small>
                        </div>
                        <div class="options">
                            <h6>${formatPrice(food.Gia)} đ</h6>
                            <a href="/food/${food.MaMonAn}">
                                <i class="fa fa-eye"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `).join('');
}

// Render reviews
function renderReviews(reviews) {
    const container = document.querySelector('.reviews-list');
    if (!container) return;

    if (reviews.length === 0) {
        container.innerHTML = '<p class="text-center text-muted py-4">Chưa có đánh giá nào</p>';
        return;
    }

    container.innerHTML = reviews.map(review => `
        <div class="review-item mb-3 p-3 border-bottom">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div>
                    <strong>${review.nguoi_dung?.HoTen || 'Người dùng'}</strong>
                    <div class="text-warning">
                        ${renderStars(review.diem_danh_gia)}
                    </div>
                </div>
                <small class="text-muted">${formatDate(review.created_at)}</small>
            </div>
            <p class="mb-1">${review.noi_dung}</p>
            ${review.hinh_anh ? `
                <img src="/storage/${review.hinh_anh}" 
                     alt="Review image" 
                     class="img-thumbnail mt-2" 
                     style="max-width: 200px;">
            ` : ''}
        </div>
    `).join('');
}

// Helper functions
function renderStars(rating) {
    let stars = '';
    for (let i = 1; i <= 5; i++) {
        if (i <= Math.floor(rating)) {
            stars += '<i class="fa fa-star text-warning"></i>';
        } else if (i === Math.ceil(rating) && rating - Math.floor(rating) >= 0.5) {
            stars += '<i class="fa fa-star-half-alt text-warning"></i>';
        } else {
            stars += '<i class="fa fa-star text-muted"></i>';
        }
    }
    return stars;
}

function formatPrice(price) {
    return new Intl.NumberFormat('vi-VN').format(price);
}

function truncate(text, length) {
    if (text.length <= length) return text;
    return text.substring(0, length) + '...';
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('vi-VN');
}

function showDetailLoading() {
    const mainContent = document.querySelector('.food-detail-section');
    if (mainContent) {
        mainContent.innerHTML = `
            <div class="text-center py-5">
                <div class="spinner-border text-warning" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-3 text-muted">Đang tải thông tin món ăn...</p>
            </div>
        `;
    }
}

function hideDetailLoading() {
    // Loading is replaced by content
}

function showDetailError(message) {
    const mainContent = document.querySelector('.food-detail-section');
    if (mainContent) {
        mainContent.innerHTML = `
            <div class="text-center py-5">
                <i class="fa fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                <p class="text-danger">${message}</p>
                <a href="/" class="btn btn-warning mt-3">Về trang chủ</a>
            </div>
        `;
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // DISABLED: API loading - using server-side rendering instead
    // Only uncomment to use API loading
    // if (window.location.pathname.includes('/food/')) {
    //     loadFoodDetail();
    // }
});
