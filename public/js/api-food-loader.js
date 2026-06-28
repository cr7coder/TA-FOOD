// API Configuration
const API_BASE_URL = '/api/v1';
let currentPage = 1;
let currentFilters = {};
let foodsData = [];
let restaurantsData = [];

// Load foods from API
async function loadFoods(page = 1, filters = {}, append = false) {
    try {
        if (!append) {
            showLoading();
        }

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
            if (append) {
                foodsData = [...foodsData, ...result.data];
                renderFoods(result.data, true);
            } else {
                foodsData = result.data;
                renderFoods(foodsData, false);
            }

            renderPagination(result);

            if (!append) {
                hideLoading();
            }
        } else {
            showError('Không thể tải danh sách món ăn');
            if (!append) {
                hideLoading();
            }
        }
    } catch (error) {
        console.error('Error loading foods:', error);
        showError('Có lỗi xảy ra khi tải dữ liệu');
        if (!append) {
            hideLoading();
        }
    }
}

// Render foods list
function renderFoods(foods, append = false) {
    const container = document.querySelector('.filters-content .row.grid');
    if (!container) return;

    if (foods.length === 0 && !append) {
        container.innerHTML = `
            <div class="col-12 text-center py-5">
                <i class="fa fa-utensils fa-3x text-muted mb-3"></i>
                <p class="text-muted">Không tìm thấy món ăn nào</p>
            </div>
        `;
        return;
    }

    const html = foods.map(food => {
        const imageSrc = food.hinh_anh_url || food.hinhAnhUrl || (food.HinhAnh ? (food.HinhAnh.startsWith('http') ? food.HinhAnh : '/images/' + food.HinhAnh) : '/images/no-image.png');
        const formattedPrice = formatPrice(food.Gia);

        // Stars HTML
        let starsHtml = renderStars(food.diem_trung_binh || 0);

        // Popular Star Badge
        let badgeHtml = '';
        if (food.diem_trung_binh >= 4.5 && (food.tong_binh_luan || 0) >= 1) {
            badgeHtml = `
                <span class="badge position-absolute" style="top: 15px; left: 15px; z-index: 10; background: linear-gradient(135deg, #ffbe33, #ff9800); color: #222831; font-weight: 800; padding: 6px 12px; border-radius: 20px; box-shadow: 0 4px 10px rgba(255, 190, 51, 0.4); font-size: 11px; border: none;">
                    <i class="fa fa-star"></i> ${parseFloat(food.diem_trung_binh).toFixed(1)} Yêu thích
                </span>
            `;
        }

        return `
            <div class="col-6 col-sm-6 col-lg-4 all ${getFoodClass(food.DanhMuc)} ${food.TrangThai === 'Còn bán' ? 'con-ban' : 'ngung-ban'}">
                <div class="box" data-detail-url="/food/${food.MaMonAn}" style="cursor: pointer;">
                    <div>
                        <div class="img-box position-relative">
                            <img src="${imageSrc}" alt="${food.TenMonAn}" loading="lazy" style="transition: transform 0.5s cubic-bezier(0.25, 0.46, 0.45, 0.94) !important;">
                            ${badgeHtml}
                        </div>
                        <div class="detail-box">
                            <h5>
                                <a href="/food/${food.MaMonAn}" class="text-decoration-none text-light">
                                    ${food.TenMonAn}
                                </a>
                            </h5>
                            ${food.nha_hang ? `
                                <div class="restaurant-name mb-2" style="font-size:12.5px; color: #ffbe33; font-weight: 500;">
                                    <i class="fa fa-store mr-1"></i> ${food.nha_hang.TenNhaHang}
                                </div>
                            ` : ''}
                            ${food.MoTa ? `<p>${truncate(food.MoTa, 20)}</p>` : ''}
                            <div class="rating mb-2">
                                ${starsHtml}
                                <small class="text-muted ms-1">(${food.tong_binh_luan || 0})</small>
                            </div>
                            <div class="options">
                                <h6>${formattedPrice} đ</h6>
                                <div class="d-flex gap-3 align-items-center">
                                    <a href="/food/${food.MaMonAn}" 
                                       class="btn btn-warning rounded-circle d-flex align-items-center justify-content-center shadow-sm"
                                       style="width: 45px; height: 45px; transition: all 0.3s ease;"
                                       onmouseover="this.style.transform='scale(1.1)'; this.style.boxShadow='0 4px 12px rgba(255, 193, 7, 0.4)';"
                                       onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='0 2px 6px rgba(0,0,0,0.1)';"
                                       title="Xem chi tiết">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                    ${food.TrangThai === 'Còn bán' ? `
                                        <button type="button"
                                            class="btn btn-warning rounded-circle d-flex align-items-center justify-content-center shadow-sm add-to-cart-btn"
                                            style="width: 45px; height: 45px; transition: all 0.3s ease;"
                                            data-id="${food.MaMonAn}"
                                            data-name="${food.TenMonAn}" 
                                            data-price="${food.Gia}"
                                            data-image="${imageSrc}"
                                            onmouseover="this.style.transform='scale(1.1)'; this.style.boxShadow='0 4px 12px rgba(255, 193, 7, 0.4)';"
                                            onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='0 2px 6px rgba(0,0,0,0.1)';"
                                            title="Thêm vào giỏ">
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
        `;
    }).join('');

    if (append) {
        container.insertAdjacentHTML('beforeend', html);
    } else {
        container.innerHTML = html;
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

// Render pagination (Shows or hides the "Xem thêm" Load More button)
function renderPagination(pagination) {
    if (!pagination) return;

    const { current_page, last_page } = pagination;
    currentPage = current_page;

    const loadMoreContainer = document.getElementById('loadMoreContainer');
    if (loadMoreContainer) {
        if (current_page < last_page) {
            loadMoreContainer.style.setProperty('display', 'block', 'important');
        } else {
            loadMoreContainer.style.setProperty('display', 'none', 'important');
        }
    }
}

// Helper functions
function slugify(text) {
    if (!text) return '';
    return text.toString().toLowerCase()
        .replace(/á|à|ả|ã|ạ|ă|ắ|ằ|ẳ|ẵ|ặ|â|ấ|ầ|ẩ|ẫ|ậ/g, 'a')
        .replace(/é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ/g, 'e')
        .replace(/i|í|ì|ỉ|ĩ|ị/g, 'i')
        .replace(/ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ/g, 'o')
        .replace(/ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự/g, 'u')
        .replace(/ý|ỳ|ỷ|ỹ|ỵ/g, 'y')
        .replace(/đ/g, 'd')
        .replace(/\s+/g, '-')
        .replace(/[^\w\-]+/g, '')
        .replace(/\-\-+/g, '-')
        .replace(/^-+/, '')
        .replace(/-+$/, '');
}

function getFoodClass(category) {
    if (!category) return '';
    return slugify(category);
}

function renderStars(rating) {
    let stars = '';
    const avgRating = parseFloat(rating) || 0;
    const floorRating = Math.floor(avgRating);
    const ceilRating = Math.ceil(avgRating);
    for (let i = 1; i <= 5; i++) {
        if (i <= floorRating) {
            stars += '<i class="fa fa-star text-warning"></i>';
        } else if (i === ceilRating && (avgRating - floorRating) >= 0.5) {
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

function filterRestaurants(searchTerm) {
    const term = (searchTerm || '').toLowerCase().trim();
    const restaurantCards = document.querySelectorAll('.restaurant_section .row > div');
    let matchCount = 0;

    let emptyMsg = document.getElementById('empty-restaurants-msg');
    if (!emptyMsg) {
        emptyMsg = document.createElement('div');
        emptyMsg.id = 'empty-restaurants-msg';
        emptyMsg.className = 'col-12 text-center py-5';
        emptyMsg.innerHTML = `
            <div class="text-center py-4">
                <i class="fa fa-store-slash fa-3x text-muted mb-3" style="opacity: 0.5;"></i>
                <p class="text-muted font-weight-bold">Không tìm thấy quán ăn nào phù hợp với từ khóa.</p>
            </div>
        `;
        emptyMsg.style.display = 'none';
        const row = document.querySelector('.restaurant_section .row');
        if (row) row.appendChild(emptyMsg);
    }

    restaurantCards.forEach(cardCol => {
        if (cardCol.id === 'empty-restaurants-msg') return;

        const nameEl = cardCol.querySelector('.rest-name');
        const nameText = nameEl ? nameEl.textContent.toLowerCase() : '';

        if (nameText.includes(term)) {
            cardCol.style.setProperty('display', 'block', 'important');
            matchCount++;
        } else {
            cardCol.style.setProperty('display', 'none', 'important');
        }
    });

    if (term !== '' && matchCount === 0) {
        emptyMsg.style.setProperty('display', 'block', 'important');
    } else {
        emptyMsg.style.setProperty('display', 'none', 'important');
    }
}

function highlightCategoryTab(searchTerm) {
    const term = (searchTerm || '').toLowerCase().trim();
    if (!term) return;

    const tabs = document.querySelectorAll('.filters_menu li');
    let matchedTab = null;

    tabs.forEach(tab => {
        if (tab.classList.contains('see-all-categories-btn')) return;
        const text = tab.textContent.toLowerCase().trim();
        if (text === term) {
            matchedTab = tab;
        }
    });

    if (!matchedTab) {
        tabs.forEach(tab => {
            if (tab.classList.contains('see-all-categories-btn')) return;
            const text = tab.textContent.toLowerCase().trim();
            if (text.includes(term) || term.includes(text)) {
                matchedTab = tab;
            }
        });
    }

    if (matchedTab) {
        tabs.forEach(t => t.classList.remove('active'));
        matchedTab.classList.add('active');

        const container = document.querySelector('.filters_menu');
        if (container) {
            const offsetLeft = matchedTab.offsetLeft - (container.clientWidth / 2) + (matchedTab.clientWidth / 2);
            container.scrollTo({ left: offsetLeft, behavior: 'smooth' });
        }
    } else {
        tabs.forEach(t => t.classList.remove('active'));
        const allTab = document.querySelector('.filters_menu li[data-filter="*"]');
        if (allTab) {
            allTab.classList.add('active');
            const container = document.querySelector('.filters_menu');
            if (container) container.scrollTo({ left: 0, behavior: 'smooth' });
        }
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function () {
    // Setup initial pagination status if rendered from PHP Blade
    if (window.initialPagination) {
        renderPagination(window.initialPagination);
    }

    // Load More button click handler
    const loadMoreBtn = document.getElementById('loadMoreBtn');
    if (loadMoreBtn) {
        loadMoreBtn.addEventListener('click', function () {
            const nextPage = currentPage + 1;

            // Toggle spinner and state
            const btnText = document.getElementById('loadMoreText');
            const spinner = document.getElementById('loadMoreSpinner');
            if (btnText && spinner) {
                btnText.innerHTML = '<i class="fa fa-spinner fa-spin mr-2"></i> Đang tải thêm...';
                spinner.classList.remove('d-none');
            }
            loadMoreBtn.disabled = true;

            loadFoods(nextPage, currentFilters, true).finally(() => {
                if (btnText && spinner) {
                    btnText.innerHTML = '<i class="fa fa-utensils mr-2"></i> Xem thêm món ngon';
                    spinner.classList.add('d-none');
                }
                loadMoreBtn.disabled = false;
            });
        });
    }

    // Search form handler
    const searchForm = document.getElementById('searchForm');
    const searchInput = document.getElementById('searchInput');

    if (searchForm && searchInput) {
        if (searchInput.value.trim() !== '') {
            highlightCategoryTab(searchInput.value.trim());
        }

        searchForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const searchTerm = searchInput.value.trim();

            if (searchTerm === '') {
                // Reset category tabs to default "Tất cả"
                const tabs = document.querySelectorAll('.filters_menu li');
                tabs.forEach(t => t.classList.remove('active'));
                const allTab = document.querySelector('.filters_menu li[data-filter="*"]');
                if (allTab) {
                    allTab.classList.add('active');
                    const container = document.querySelector('.filters_menu');
                    if (container) container.scrollTo({ left: 0, behavior: 'smooth' });
                }
            }

            loadFoods(1, gatherAdvancedFilters(), false);
            filterRestaurants(searchTerm);
            
            if (searchTerm) {
                highlightCategoryTab(searchTerm);
            }

            const foodSection = document.querySelector('.food_section');
            if (foodSection) {
                foodSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });

        searchInput.addEventListener('input', function () {
            const val = this.value.trim();
            filterRestaurants(val);

            if (val === '') {
                // Reset category tabs to default "Tất cả"
                const tabs = document.querySelectorAll('.filters_menu li');
                tabs.forEach(t => t.classList.remove('active'));
                const allTab = document.querySelector('.filters_menu li[data-filter="*"]');
                if (allTab) {
                    allTab.classList.add('active');
                    const container = document.querySelector('.filters_menu');
                    if (container) container.scrollTo({ left: 0, behavior: 'smooth' });
                }

                loadFoods(1, gatherAdvancedFilters(), false);
            }
        });
    }

    // Advanced Filters logic
    function gatherAdvancedFilters() {
        const filters = {};
        
        // Category (from active tab)
        const activeTab = document.querySelector('.filters_menu li.active');
        if (activeTab && activeTab.getAttribute('data-filter') !== '*') {
            if (!activeTab.classList.contains('see-all-categories-btn')) {
                filters.category = activeTab.textContent.trim();
            }
        }

        // Search term
        const searchInput = document.getElementById('searchInput');
        if (searchInput && searchInput.value.trim() !== '') {
            filters.search = searchInput.value.trim();
        }

        // Price
        const priceFilter = document.getElementById('priceFilter');
        if (priceFilter && priceFilter.value) {
            const parts = priceFilter.value.split('-');
            if (parts[0]) filters.min_price = parts[0];
            if (parts[1]) filters.max_price = parts[1];
        }

        // Rating
        const ratingFilter = document.getElementById('ratingFilter');
        if (ratingFilter && ratingFilter.value) {
            filters.min_rating = ratingFilter.value;
        }

        // Sort
        const sortFilter = document.getElementById('sortFilter');
        if (sortFilter && sortFilter.value) {
            filters.sort_by = sortFilter.value;
        }

        return filters;
    }

    // Attach click events to custom dropdown filter options
    const filterOptions = document.querySelectorAll('.filter-option');
    filterOptions.forEach(option => {
        option.addEventListener('click', function(e) {
            e.preventDefault();
            
            const targetInputId = this.getAttribute('data-target');
            const targetInput = document.querySelector(targetInputId);
            const value = this.getAttribute('data-value');
            const text = this.getAttribute('data-text');
            
            if (targetInput) {
                targetInput.value = value;
                
                // Update active class
                const parentMenu = this.closest('.dropdown-menu');
                if (parentMenu) {
                    parentMenu.querySelectorAll('.filter-option').forEach(opt => opt.classList.remove('active'));
                }
                this.classList.add('active');
                
                // Update button text
                const dropdownBtn = this.closest('.custom-filter-dropdown').querySelector('.dropdown-toggle .filter-text');
                if (dropdownBtn) {
                    dropdownBtn.textContent = text;
                }
                
                // Trigger load
                loadFoods(1, gatherAdvancedFilters(), false);
                
                // Scroll
                const foodSection = document.querySelector('.food_section');
                if (foodSection) {
                    foodSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            }
        });
    });

    // Reset Filters button
    const resetFiltersBtn = document.getElementById('resetFiltersBtn');
    if (resetFiltersBtn) {
        resetFiltersBtn.addEventListener('click', function() {
            // Reset hidden inputs
            if (document.getElementById('priceFilter')) document.getElementById('priceFilter').value = '';
            if (document.getElementById('ratingFilter')) document.getElementById('ratingFilter').value = '';
            if (document.getElementById('sortFilter')) document.getElementById('sortFilter').value = 'best_seller';
            
            // Reset UI for dropdowns
            document.querySelectorAll('.custom-filter-dropdown').forEach(dropdown => {
                dropdown.querySelectorAll('.filter-option').forEach(opt => opt.classList.remove('active'));
                const defaultOpt = dropdown.querySelector('.filter-option[data-value=""]');
                const bestSellerOpt = dropdown.querySelector('.filter-option[data-value="best_seller"]');
                
                if (defaultOpt) {
                    defaultOpt.classList.add('active');
                    const text = defaultOpt.getAttribute('data-text');
                    const btn = dropdown.querySelector('.dropdown-toggle .filter-text');
                    if (btn) btn.textContent = text;
                } else if (bestSellerOpt) {
                    bestSellerOpt.classList.add('active');
                    const text = bestSellerOpt.getAttribute('data-text');
                    const btn = dropdown.querySelector('.dropdown-toggle .filter-text');
                    if (btn) btn.textContent = text;
                }
            });
            
            // Reset category
            const filterItems = document.querySelectorAll('.filters_menu li');
            filterItems.forEach(i => i.classList.remove('active'));
            const allTab = document.querySelector('.filters_menu li[data-filter="*"]');
            if (allTab) allTab.classList.add('active');
            
            // Reset search
            if (document.getElementById('searchInput')) document.getElementById('searchInput').value = '';
            
            loadFoods(1, gatherAdvancedFilters(), false);
        });
    }

    // Category filter tabs - Load dynamically from API
    const filterItems = document.querySelectorAll('.filters_menu li');
    filterItems.forEach(item => {
        item.addEventListener('click', function () {
            if (this.classList.contains('see-all-categories-btn')) return;

            filterItems.forEach(i => i.classList.remove('active'));
            this.classList.add('active');

            // Clear current search input so it doesn't conflict
            const searchInput = document.getElementById('searchInput');
            if (searchInput) searchInput.value = '';

            // Load dynamically from API
            loadFoods(1, gatherAdvancedFilters(), false);
        });
    });
});
