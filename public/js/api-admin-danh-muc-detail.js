document.addEventListener('DOMContentLoaded', function () {
    const categoryId = window.categoryId ?? null;
    const isEditPage = document.getElementById('editCategoryForm') !== null;
    const isCreatePage = document.getElementById('createCategoryForm') !== null;
    const isShowPage = document.getElementById('categoryDetailContainer') !== null;

    // ── Initial load based on view context ─────────────────────────────────────
    if (categoryId) {
        if (isEditPage) {
            loadCategoryForm(categoryId);
        } else if (isShowPage) {
            loadCategoryDetail(categoryId);
        }
    }

    // ── Form submits ──────────────────────────────────────────────────────────
    document.getElementById('createCategoryForm')?.addEventListener('submit', function (e) {
        e.preventDefault();
        submitCategoryForm('/api/v1/admin/danh-muc', 'POST', this);
    });

    document.getElementById('editCategoryForm')?.addEventListener('submit', function (e) {
        e.preventDefault();
        submitCategoryForm(`/api/v1/admin/danh-muc/${categoryId}`, 'PUT', this);
    });

    // =========================================================================
    // FETCH DETAILS FOR FORM POPULATION
    // =========================================================================
    async function loadCategoryForm(id) {
        try {
            const res = await fetch(`/api/v1/admin/danh-muc/${id}`, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            const json = await res.json();
            if (json.success) {
                const c = json.data;
                document.getElementById('TenDanhMuc').value = c.TenDanhMuc || '';
                const elSlug = document.getElementById('Slug');
                if (elSlug) elSlug.value = c.Slug || '';
                document.getElementById('MoTa').value = c.MoTa || '';
                document.getElementById('HinhAnh').value = c.HinhAnh || '';
                
                const statusSelect = document.getElementById('TrangThai');
                if (statusSelect) statusSelect.value = c.TrangThai || 'Hoạt động';

                // Display dynamic thumbnail if element exists
                updateThumbnailPreview(c.HinhAnh, c.TenDanhMuc);
            } else {
                showToast(json.message || 'Không thể lấy thông tin danh mục', 'danger');
            }
        } catch (err) {
            console.error(err);
            showToast('Lỗi khi tải thông tin danh mục từ server', 'danger');
        }
    }

    // =========================================================================
    // FETCH DETAILS FOR SHOW VIEW
    // =========================================================================
    async function loadCategoryDetail(id) {
        const container = document.getElementById('categoryDetailContainer');
        if (!container) return;

        try {
            const res = await fetch(`/api/v1/admin/danh-muc/${id}`, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            const json = await res.json();
            if (json.success) {
                const c = json.data;
                
                const imgPath = c.HinhAnh ? c.HinhAnh : '/images/categories/default.png';
                const statusBadge = c.TrangThai === 'Hoạt động'
                    ? `<span class="badge bg-success py-2 px-3"><i class="fas fa-check-circle me-1"></i>Hoạt động</span>`
                    : `<span class="badge bg-danger py-2 px-3"><i class="fas fa-minus-circle me-1"></i>Đã khóa</span>`;

                container.innerHTML = `
                    <div class="row g-4 align-items-center">
                        <div class="col-md-4 text-center">
                            <div class="mx-auto shadow-sm" style="width: 200px; height: 200px; border-radius: 20px; overflow: hidden; background: #f3f4f6; border: 3px solid var(--border); display: flex; align-items: center; justify-content: center;">
                                <img src="${imgPath}" alt="${c.TenDanhMuc}" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.src='https://placehold.co/200x200?text=${encodeURIComponent(c.TenDanhMuc)}'">
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="mb-3">
                                <span class="text-muted small text-uppercase fw-bold d-block">Trạng thái</span>
                                ${statusBadge}
                            </div>
                            <h2 class="fw-bold mb-1">${c.TenDanhMuc}</h2>
                            <p class="text-muted mb-2">Slug: <code class="text-primary fs-6">${c.Slug || '—'}</code></p>
                            <p class="text-muted mb-3">Mã danh mục: #<b>${c.MaDanhMuc}</b></p>
                            
                            <div class="card p-3 border-0 bg-light-surface mb-3" style="border-radius:12px;">
                                <span class="text-muted small text-uppercase fw-bold d-block mb-1">Mô tả chi tiết</span>
                                <p class="mb-0 fs-5 text-dark">${c.MoTa || '<em>Chưa có mô tả chi tiết cho danh mục này.</em>'}</p>
                            </div>

                            <div class="row g-3 text-muted small">
                                <div class="col-sm-6">
                                    <i class="fas fa-calendar-plus me-1"></i> Ngày tạo: <b>${fmtDate(c.created_at)}</b>
                                </div>
                                <div class="col-sm-6">
                                    <i class="fas fa-calendar-check me-1"></i> Cập nhật cuối: <b>${fmtDate(c.updated_at)}</b>
                                </div>
                            </div>
                        </div>
                    </div>`;
            } else {
                container.innerHTML = `<div class="alert alert-danger"><i class="fas fa-exclamation-circle me-1"></i>${json.message || 'Không tìm thấy danh mục.'}</div>`;
            }
        } catch (err) {
            console.error(err);
            container.innerHTML = `<div class="alert alert-danger"><i class="fas fa-exclamation-circle me-1"></i>Không thể kết nối server.</div>`;
        }
    }

    // =========================================================================
    // SUBMIT CREATE/UPDATE FORM
    // =========================================================================
    async function submitCategoryForm(url, method, formElement) {
        clearValidationErrors();
        const submitBtn = formElement.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = `<span class="spinner-border spinner-border-sm me-1" role="status"></span> Đang lưu...`;
        }

        try {
            const formData = new FormData(formElement);
            
            // Laravel PUT request multipart file upload compatibility fix
            let fetchUrl = url;
            let fetchMethod = method;
            if (method.toUpperCase() === 'PUT') {
                formData.append('_method', 'PUT');
                fetchMethod = 'POST';
            }

            const res = await fetch(fetchUrl, {
                method: fetchMethod,
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: formData
            });

            const json = await res.json();
            if (json.success) {
                showToast(json.message || 'Lưu danh mục thành công!', 'success');
                setTimeout(() => {
                    window.location.href = '/admin/danh-muc';
                }, 1000);
            } else {
                if (json.errors) {
                    showValidationErrors(json.errors);
                } else {
                    showToast(json.message || 'Lỗi khi lưu danh mục', 'danger');
                }
                
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = `<i class="fas fa-save me-1"></i> Lưu lại`;
                }
            }
        } catch (err) {
            console.error(err);
            showToast('Không thể kết nối server', 'danger');
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = `<i class="fas fa-save me-1"></i> Lưu lại`;
            }
        }
    }

    // ── File Choose Live Preview ────────────────────────────────────────────────
    document.getElementById('HinhAnhFile')?.addEventListener('change', function () {
        const file = this.files[0];
        if (file) {
            const previewImg = document.getElementById('previewImage');
            if (previewImg) {
                previewImg.src = URL.createObjectURL(file);
            }
        }
    });

    // ── Input Live Thumbnail Preview ──────────────────────────────────────────
    document.getElementById('HinhAnh')?.addEventListener('input', function () {
        const nameVal = document.getElementById('TenDanhMuc')?.value || 'Preview';
        updateThumbnailPreview(this.value.trim(), nameVal);
    });

    // =========================================================================
    // UTILITIES
    // =========================================================================
    function updateThumbnailPreview(url, name) {
        const previewImg = document.getElementById('previewImage');
        if (!previewImg) return;
        
        if (url) {
            previewImg.src = url;
            previewImg.onerror = function() {
                this.src = `https://placehold.co/150x150?text=${encodeURIComponent(name)}`;
            };
        } else {
            previewImg.src = `https://placehold.co/150x150?text=${encodeURIComponent(name)}`;
        }
    }

    function showValidationErrors(errors) {
        Object.keys(errors).forEach(key => {
            const input = document.getElementById(key);
            if (input) {
                input.classList.add('is-invalid');
                const feedback = input.nextElementSibling;
                if (feedback && feedback.classList.contains('invalid-feedback')) {
                    feedback.textContent = errors[key][0];
                } else {
                    const div = document.createElement('div');
                    div.className = 'invalid-feedback';
                    div.textContent = errors[key][0];
                    input.parentNode.appendChild(div);
                }
            }
        });
        showToast('Vui lòng kiểm tra lại thông tin biểu mẫu', 'danger');
    }

    function clearValidationErrors() {
        document.querySelectorAll('.is-invalid').forEach(input => {
            input.classList.remove('is-invalid');
        });
        document.querySelectorAll('.invalid-feedback').forEach(feedback => {
            feedback.textContent = '';
        });
    }

    function fmtDate(str) {
        if (!str) return '—';
        return new Date(str).toLocaleDateString('vi-VN') + ' ' + new Date(str).toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' });
    }

    // Auto clear error class on typing
    const formInputs = ['TenDanhMuc', 'Slug', 'MoTa', 'HinhAnh'];
    formInputs.forEach(id => {
        document.getElementById(id)?.addEventListener('input', function() {
            this.classList.remove('is-invalid');
            const feedback = this.nextElementSibling;
            if (feedback && feedback.classList.contains('invalid-feedback')) {
                feedback.textContent = '';
            }
        });
    });

    function showToast(msg, type = 'success') {
        if (window.showAdminToast) {
            window.showAdminToast(msg, type);
        } else {
            const el = document.getElementById('actionToast');
            if (!el) return;
            document.getElementById('toastMessage').textContent = msg;
            el.className = `toast align-items-center text-white border-0 bg-${type}`;
            new bootstrap.Toast(el).show();
        }
    }
});
