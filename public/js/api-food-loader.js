// API Configuration
const API_BASE_URL = '/api/v1';
let currentPage = 1;
let foodsData = [];
let restaurantsData = [];

// Load foods from API
async function loadFoods(page = 1, filters = {}) {
    try {
        showLoading();
        
        // Build query parameters
        const params = new URLSearchParams({
            page: page,
            per_page: 12,
            ...filters
        });

        const response = await fetch(`${API_BASE_URL}/foods?${params}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        });

        const result = await response.json();

        if (result.success) {
            foodsData = result.data.foods.data;
            restaurantsData = result.data.restaurants;
            
            renderFoods(foodsData);
            renderRestaurants(restaurantsData);
            renderPagination(result.data.foods);
            
            hideLoading();
        } else {
            showError('Không thể tải danh sách món ăn');
            hideLoading();
        }
    } catch (error) {
        console.error('Error loading foods:', error);
        showError('Có lỗi xảy ra khi tải dữ liệu');
        hideLoading();
    }
}

// Render foods list
function renderFoods(foods) {
    const container = document.querySelector('.filters-content .row.grid');
    if (!container) return;

    if (foods.length === 0) {
        container.innerHTML = `
            <div class="col-12 text-center py-5">
                <i class="fa fa-utensils fa-3x text-muted mb-3"></i>
                <p class="text-muted">Không tìm thấy món ăn nào</p>
            </div>
        `;
        return;
    }

    container.innerHTML = foods.map(food => `
        <div class="col-sm-6 col-lg-4 all ${getFoodClass(food.DanhMuc)} ${food.TrangThai === 'Còn bán' ? 'con-ban' : 'ngung-ban'}">
            <div class="box">
                <div>
                    <div class="img-box">
                        <img src="${food.HinhAnh ? '/images/' + food.HinhAnh : '/images/no-image.png'}" 
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
                            <div class="d-flex gap-3 align-items-center">
                                <a href="/food/${food.MaMonAn}" 
                                   class="btn btn-warning rounded-circle d-flex align-items-center justify-content-center shadow-sm"
                                   style="width: 45px; height: 45px;">
                                    <i class="fa fa-eye"></i>
                                </a>
                                ${food.TrangThai === 'Còn bán' ? `
                                    <button type="button"
                                        class="btn btn-warning rounded-circle d-flex align-items-center justify-content-center shadow-sm add-to-cart-btn"
                                        style="width: 45px; height: 45px;"
                                        data-id="${food.MaMonAn}"
                                        data-name="${food.TenMonAn}" 
                                        data-price="${food.Gia}"
                                        data-image="${food.HinhAnh ? '/images/' + food.HinhAnh : '/images/no-image.png'}">
                                        <i class="fa fa-shopping-cart"></i>
                                    </button>
                                ` : `
                                    <span class="badge bg-danger">Ngừng bán</span>
                                `}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `).join('');

    // Re-attach cart button event listeners
    attachCartButtonListeners();
}

// Render restaurants list
function renderRestaurants(restaurants) {
    const container = document.querySelector('.restaurant_section .row');
    if (!container) return;

    if (restaurants.length === 0) {
        container.innerHTML = `
            <div class="col-12">
                <p class="text-center text-muted">Chưa có nhà hàng nào.</p>
            </div>
        `;
        return;
    }

    container.innerHTML = restaurants.map((res, index) => `
        <div class="col-sm-6 col-lg-4 mb-4" style="--delay: ${index * 80}ms;">
            <div class="card restaurant-card h-100 shadow-sm">
                <span class="rest-badge">
                    <i class="fa fa-utensils"></i> ${res.mon_an_count} món
                </span>
                <div class="card-body d-flex flex-column">
                    <div class="rest-avatar" data-letter="${res.TenNhaHang.charAt(0)}"></div>
                    <h5 class="card-title mb-2 rest-name" style="font-weight: 600;">${res.TenNhaHang}</h5>
                    <p class="card-text text-muted mb-1 rest-address">
                        <i class="fa fa-map-marker"></i>
                        ${truncate(res.DiaChi, 80)}
                    </p>
                    ${res.SoDienThoai ? `
                        <p class="card-text text-muted rest-phone">
                            <i class="fa fa-phone"></i> ${res.SoDienThoai}
                        </p>
                    ` : ''}
                    <div class="mt-auto d-flex justify-content-between align-items-center">
                        <small class="text-muted"></small>
                        <a href="/restaurant/${res.MaNhaHang}" class="btn btn-warning btn-sm rest-cta">
                            <i class="fa fa-eye"></i> Xem món
                        </a>
                    </div>
                </div>
            </div>
        </div>
    `).join('');
}

// Render pagination
function renderPagination(pagination) {
    // Implement pagination if needed
    currentPage = pagination.current_page;
}

// Helper functions
function getFoodClass(category) {
    const categoryMap = {
        'Cơm': 'com',
        'Bún': 'bun',
        'Phở': 'pho',
        'Mì': 'mi',
        'Trà': 'tra'
    };
    return categoryMap[category] || '';
}

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

function showLoading() {
    const container = document.querySelector('.filters-content .row.grid');
    if (container) {
        container.innerHTML = `
            <div class="col-12 text-center py-5">
                <div class="spinner-border text-warning" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-3 text-muted">Đang tải món ăn...</p>
            </div>
        `;
    }
}

function hideLoading() {
    // Loading is replaced by content
}

function showError(message) {
    const container = document.querySelector('.filters-content .row.grid');
    if (container) {
        container.innerHTML = `
            <div class="col-12 text-center py-5">
                <i class="fa fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                <p class="text-danger">${message}</p>
                <button class="btn btn-warning mt-3" onclick="loadFoods()">Thử lại</button>
            </div>
        `;
    }
}

function attachCartButtonListeners() {
    // Re-attach event listeners for add to cart buttons
    // This should integrate with existing cart functionality
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // DISABLED: API loading - using server-side rendering instead
    // Only load if we're on the foods index page
    // if (document.querySelector('.food_section')) {
    //     loadFoods();
    // }

    // Category filter - keep this for client-side filtering
    const filterItems = document.querySelectorAll('.filters_menu li');
    filterItems.forEach(item => {
        item.addEventListener('click', function() {
            filterItems.forEach(i => i.classList.remove('active'));
            this.classList.add('active');
            
            // Use Isotope filtering instead of API
            // const category = this.textContent.trim();
            // const filters = category === 'Tất cả' ? {} : { category: category };
            // loadFoods(1, filters);
        });
    });
});
