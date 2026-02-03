// API Configuration
const API_BASE_URL = '/api/v1';
let currentPage = 1;
let currentFilters = {};
let foodsData = [];
let restaurantsData = [];

// Load foods from API
async function loadFoods(page = 1, filters = {}) {
    try {
        showLoading();
        
        // Store current filters
        currentFilters = filters;
        currentPage = page;
        
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

        if (response.ok) {
            // API returns paginated data directly
            foodsData = result.data;
            
            renderFoods(foodsData);
            renderPagination(result);
            
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

    // Re-attach cart button event listeners for newly rendered foods
    if (typeof window.attachAddToCartListeners === 'function') {
        window.attachAddToCartListeners();
    }
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
    const paginationContainer = document.querySelector('.pagination-container');
    if (!paginationContainer || !pagination) return;

    const { current_page, last_page, per_page, total } = pagination;
    
    if (last_page <= 1) {
        paginationContainer.innerHTML = '';
        return;
    }

    let paginationHTML = '<nav><ul class="pagination justify-content-center">';
    
    // Previous button
    if (current_page > 1) {
        paginationHTML += `
            <li class="page-item">
                <a class="page-link" href="#" onclick="loadFoods(${current_page - 1}, currentFilters); return false;">
                    <i class="fa fa-chevron-left"></i>
                </a>
            </li>
        `;
    }
    
    // Page numbers
    const startPage = Math.max(1, current_page - 2);
    const endPage = Math.min(last_page, current_page + 2);
    
    for (let i = startPage; i <= endPage; i++) {
        paginationHTML += `
            <li class="page-item ${i === current_page ? 'active' : ''}">
                <a class="page-link" href="#" onclick="loadFoods(${i}, currentFilters); return false;">
                    ${i}
                </a>
            </li>
        `;
    }
    
    // Next button
    if (current_page < last_page) {
        paginationHTML += `
            <li class="page-item">
                <a class="page-link" href="#" onclick="loadFoods(${current_page + 1}, currentFilters); return false;">
                    <i class="fa fa-chevron-right"></i>
                </a>
            </li>
        `;
    }
    
    paginationHTML += '</ul></nav>';
    paginationHTML += `<p class="text-center text-muted mt-2">Hiển thị ${per_page} trong tổng ${total} món ăn</p>`;
    
    paginationContainer.innerHTML = paginationHTML;
    currentPage = current_page;
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
    if (!text) return '';
    if (text.length <= length) return text;
    return text.substring(0, length) + '...';
}

function showLoading() {
    const spinner = document.getElementById('loadingSpinner');
    const errorMsg = document.getElementById('errorMessage');
    const content = document.querySelector('.filters-content');
    
    if (spinner) spinner.style.display = 'block';
    if (errorMsg) errorMsg.style.display = 'none';
    if (content) content.style.opacity = '0.5';
}

function hideLoading() {
    const spinner = document.getElementById('loadingSpinner');
    const content = document.querySelector('.filters-content');
    
    if (spinner) spinner.style.display = 'none';
    if (content) content.style.opacity = '1';
}

function showError(message) {
    const errorMsg = document.getElementById('errorMessage');
    const errorText = document.getElementById('errorText');
    const spinner = document.getElementById('loadingSpinner');
    const content = document.querySelector('.filters-content');
    
    if (spinner) spinner.style.display = 'none';
    if (errorMsg) errorMsg.style.display = 'block';
    if (errorText) errorText.textContent = message;
    if (content) content.style.display = 'none';
}

function attachCartButtonListeners() {
    // Re-attach event listeners for add to cart buttons
    // Cart button listeners are handled by _cart-scripts.blade.php
}

function attachCartButtonListeners() {
    // Re-attach event listeners for add to cart buttons
    // This should integrate with existing cart functionality
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Search form handler
    const searchForm = document.getElementById('searchForm');
    const searchInput = document.getElementById('searchInput');
    
    if (searchForm && searchInput) {
        searchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const searchTerm = searchInput.value.trim();
            
            if (searchTerm) {
                loadFoods(1, { search: searchTerm });
            } else {
                loadFoods(1);
            }
            
            // Scroll to food section
            const foodSection = document.querySelector('.food_section');
            if (foodSection) {
                foodSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });

        // Clear search on input clear
        searchInput.addEventListener('input', function() {
            if (this.value === '') {
                loadFoods(1);
            }
        });
    }

    // DISABLED: Auto API loading on page load - using server-side rendering instead
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
