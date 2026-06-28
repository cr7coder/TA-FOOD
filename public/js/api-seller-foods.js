/**
 * Seller Foods API Loader
 * Handles fetching, rendering, searching, and deleting foods via REST API
 */

document.addEventListener('DOMContentLoaded', function() {
    // Selectors
    const tableBody = document.getElementById('foods-table-body');
    const paginationContainer = document.getElementById('pagination-container');
    const searchForm = document.getElementById('foods-search-form');
    const searchInput = document.getElementById('search-input');
    const resetBtn = document.getElementById('reset-btn');

    // State
    let currentPage = 1;
    let currentSearch = '';

    // Initialize
    loadFoods();

    // Event Listeners
    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            currentSearch = searchInput.value.trim();
            currentPage = 1;
            loadFoods();
        });
    }

    if (resetBtn) {
        resetBtn.addEventListener('click', function() {
            searchInput.value = '';
            currentSearch = '';
            currentPage = 1;
            loadFoods();
        });
    }

    /**
     * Fetch foods from API
     */
    async function loadFoods() {
        showLoading();
        try {
            const response = await fetch(`/api/v1/seller/foods?page=${currentPage}&search=${encodeURIComponent(currentSearch)}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const result = await response.json();

            if (result.success) {
                renderFoods(result.data.foods);
                renderPagination(result.data.pagination);
            } else {
                showError(result.message || 'Có lỗi xảy ra khi tải dữ liệu');
            }
        } catch (error) {
            console.error('Error fetching foods:', error);
            showError('Không thể kết nối đến server. Vui lòng thử lại sau.');
        }
    }

    /**
     * Render food rows into table
     */
    function renderFoods(foods) {
        if (!foods || foods.length === 0) {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="8" class="text-center text-muted py-4">
                        <i class="fas fa-info-circle me-1"></i> Chưa có món ăn nào phù hợp với tìm kiếm.
                    </td>
                </tr>
            `;
            return;
        }

        let html = '';
        foods.forEach(food => {
            const statusBadgeClass = food.TrangThai === 'Còn bán' ? 'bg-success' : 'bg-secondary';
            const imageUrl = food.hinh_anh_url || food.hinhAnhUrl || (food.HinhAnh ? (food.HinhAnh.startsWith('http') ? food.HinhAnh : `/images/${food.HinhAnh}`) : '');
            const priceFormatted = new Intl.NumberFormat('vi-VN').format(food.Gia);

            // Flag inactive categories in merchant dashboard
            let categoryBadge = `<span class="badge bg-light text-dark border">${food.DanhMuc}</span>`;
            let statusAddon = '';
            
            if (food.is_category_active === false) {
                categoryBadge = `
                    <span class="badge bg-danger text-white border" title="Danh mục này đã bị xóa/khóa! Hãy sửa món ăn để gán sang danh mục hoạt động khác.">
                        ${food.DanhMuc} <i class="fas fa-exclamation-triangle ms-1"></i>
                    </span>
                `;
                statusAddon = `
                    <span class="badge bg-warning text-dark ms-1" title="Bị ẩn với khách hàng vì danh mục cha không hoạt động">
                        Bị ẩn <i class="fas fa-eye-slash"></i>
                    </span>
                `;
            }

            const restaurantName = food.nha_hang ? food.nha_hang.TenNhaHang : 'Không xác định';

            html += `
                <tr>
                    <td>${food.MaMonAn}</td>
                    <td class="fw-bold">${food.TenMonAn}</td>
                    <td><span class="text-secondary small fw-semibold"><i class="fas fa-store me-1 text-warning"></i>${restaurantName}</span></td>
                    <td>${categoryBadge}</td>
                    <td>${priceFormatted} đ</td>
                    <td>
                        <span class="badge ${statusBadgeClass}">${food.TrangThai}</span>
                        ${statusAddon}
                    </td>
                    <td>
                        ${food.HinhAnh ? `
                            <img src="${imageUrl}" alt="${food.TenMonAn}"
                                style="width:50px;height:40px;object-fit:cover;border-radius:6px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                        ` : '<span class="text-muted small">No image</span>'}
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            <button type="button" class="btn btn-sm btn-outline-info view-food-btn"
                                data-id="${food.MaMonAn}" title="Xem chi tiết">
                                <i class="fas fa-eye"></i>
                            </button>
                            <a href="/seller/foods/${food.MaMonAn}/edit"
                                class="btn btn-sm btn-outline-warning" title="Chỉnh sửa">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button type="button" class="btn btn-sm btn-outline-danger delete-food-btn" 
                                data-id="${food.MaMonAn}" data-name="${food.TenMonAn}" title="Xóa">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        });

        tableBody.innerHTML = html;

        // Bind view detail buttons
        document.querySelectorAll('.view-food-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                showFoodDetail(id);
            });
        });

        // Bind delete buttons
        document.querySelectorAll('.delete-food-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const name = this.getAttribute('data-name');
                confirmDelete(id, name);
            });
        });
    }

    /**
     * Render pagination controls
     */
    function renderPagination(pagination) {
        if (!pagination || pagination.last_page <= 1) {
            paginationContainer.innerHTML = '';
            return;
        }

        let html = '<nav aria-label="Page navigation"><ul class="pagination pagination-sm mb-0 shadow-sm">';
        
        // Previous page
        html += `
            <li class="page-item ${pagination.current_page === 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${pagination.current_page - 1}">
                    <i class="fas fa-chevron-left"></i>
                </a>
            </li>
        `;

        // Page numbers
        for (let i = 1; i <= pagination.last_page; i++) {
            if (i === 1 || i === pagination.last_page || (i >= pagination.current_page - 2 && i <= pagination.current_page + 2)) {
                html += `
                    <li class="page-item ${i === pagination.current_page ? 'active' : ''}">
                        <a class="page-link" href="#" data-page="${i}">${i}</a>
                    </li>
                `;
            } else if (i === pagination.current_page - 3 || i === pagination.current_page + 3) {
                html += '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }
        }

        // Next page
        html += `
            <li class="page-item ${pagination.current_page === pagination.last_page ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${pagination.current_page + 1}">
                    <i class="fas fa-chevron-right"></i>
                </a>
            </li>
        `;

        html += '</ul></nav>';
        paginationContainer.innerHTML = html;

        // Bind pagination clicks
        paginationContainer.querySelectorAll('.page-link').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const page = parseInt(this.getAttribute('data-page'));
                if (page && page !== currentPage) {
                    currentPage = page;
                    loadFoods();
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }
            });
        });
    }

    /**
     * Handle food deletion
     */
    function confirmDelete(id, name) {
        Swal.fire({
            title: 'Xác nhận xóa?',
            text: `Bạn có chắc chắn muốn xóa món: "${name}"? Hành động này không thể hoàn tác!`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Đồng ý xóa',
            cancelButtonText: 'Hủy'
        }).then(async (result) => {
            if (result.isConfirmed) {
                try {
                    const response = await fetch(`/api/v1/seller/foods/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    const resData = await response.json();

                    if (resData.success) {
                        Swal.fire({
                            title: 'Đã xóa!',
                            text: resData.message || 'Đã xóa món ăn thành công.',
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        });
                        // Refresh list
                        loadFoods();
                    } else {
                        Swal.fire({
                            title: 'Thất bại!',
                            text: resData.message || 'Không thể xóa món ăn.',
                            icon: 'error'
                        });
                    }
                } catch (error) {
                    console.error('Error deleting food:', error);
                    Swal.fire({
                        title: 'Lỗi hệ thống!',
                        text: 'Có lỗi xảy ra khi thực hiện xóa món ăn.',
                        icon: 'error'
                    });
                }
            }
        });
    }

    /**
     * Show food detail in modern modal
     */
    async function showFoodDetail(id) {
        // Select elements
        const modalElement = document.getElementById('food-detail-modal');
        if (!modalElement) return;
        
        const modal = bootstrap.Modal.getOrCreateInstance(modalElement);
        
        const spinner = document.getElementById('modal-loading-spinner');
        const content = document.getElementById('modal-food-content');
        
        // Show modal and start with loading state
        spinner.classList.remove('d-none');
        content.classList.add('d-none');
        modal.show();
        
        try {
            const response = await fetch(`/api/v1/seller/foods/${id}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            const result = await response.json();
            
            if (result.success) {
                const food = result.data;
                
                // Populate elements
                const imgElement = document.getElementById('detail-food-img');
                imgElement.src = food.hinh_anh_url || food.hinhAnhUrl || (food.HinhAnh ? (food.HinhAnh.startsWith('http') ? food.HinhAnh : `/images/${food.HinhAnh}`) : '');
                imgElement.alt = food.TenMonAn;
                
                // Status premium badge
                const statusContainer = document.getElementById('detail-food-status');
                let statusHtml = '';
                if (food.TrangThai === 'Còn bán') {
                    statusHtml = `<span class="status-badge-premium active">Còn bán</span>`;
                } else {
                    statusHtml = `<span class="status-badge-premium inactive">Ngừng bán</span>`;
                }
                
                if (food.is_category_active === false) {
                    statusHtml += `<span class="status-badge-premium hidden ms-2" title="Danh mục của món ăn này hiện đang bị khóa hoặc ẩn! Món ăn tạm thời bị ẩn với khách hàng.">Bị ẩn</span>`;
                }
                statusContainer.innerHTML = statusHtml;
                
                // Star ratings and feedback
                document.getElementById('detail-food-rating').textContent = (food.diem_trung_binh || 0).toFixed(1);
                document.getElementById('detail-food-reviews-count').textContent = food.tong_binh_luan || 0;
                
                // Text details
                document.getElementById('detail-food-name').textContent = food.TenMonAn;
                document.getElementById('detail-food-category').textContent = food.DanhMuc || 'Chưa phân loại';
                document.getElementById('detail-food-price').textContent = new Intl.NumberFormat('vi-VN').format(food.Gia) + ' đ';
                document.getElementById('detail-food-restaurant').textContent = food.nha_hang ? food.nha_hang.TenNhaHang : 'Không xác định';
                document.getElementById('detail-food-desc').textContent = food.MoTa || 'Chưa có mô tả cho món ăn này.';
                
                // Timestamps
                const createdDate = food.created_at ? new Date(food.created_at).toLocaleDateString('vi-VN') : '--/--/----';
                const updatedDate = food.updated_at ? new Date(food.updated_at).toLocaleDateString('vi-VN') : '--/--/----';
                document.getElementById('detail-food-created').textContent = createdDate;
                document.getElementById('detail-food-updated').textContent = updatedDate;
                
                // Edit button shortcut
                document.getElementById('detail-food-edit-btn').href = `/seller/foods/${food.MaMonAn}/edit`;
                
                // Transition states
                spinner.classList.add('d-none');
                content.classList.remove('d-none');
            } else {
                Swal.fire({
                    title: 'Lỗi!',
                    text: result.message || 'Không thể lấy thông tin món ăn.',
                    icon: 'error',
                    confirmButtonText: 'Đóng'
                });
                modal.hide();
            }
        } catch (error) {
            console.error('Error getting food detail:', error);
            Swal.fire({
                title: 'Lỗi!',
                text: 'Không thể kết nối với máy chủ. Vui lòng thử lại sau.',
                icon: 'error',
                confirmButtonText: 'Đóng'
            });
            modal.hide();
        }
    }

    function showLoading() {
        tableBody.innerHTML = `
            <tr>
                <td colspan="8" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2 text-muted">Đang tải danh sách món ăn...</p>
                </td>
            </tr>
        `;
    }

    function showError(message) {
        tableBody.innerHTML = `
            <tr>
                <td colspan="8" class="text-center py-5 text-danger">
                    <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>
                    <p>${message}</p>
                    <button class="btn btn-sm btn-outline-primary" onclick="location.reload()">Thử lại</button>
                </td>
            </tr>
        `;
    }
});
