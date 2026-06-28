document.addEventListener('DOMContentLoaded', function () {
    let currentPage = 1;
    let currentSearch = '';
    let currentStatus = '';
    let deleteTargetId = null;

    // ── Initial load ──────────────────────────────────────────────────────────
    fetchCategories();

    // ── Filter form ───────────────────────────────────────────────────────────
    document.getElementById('filterForm')?.addEventListener('submit', function (e) {
        e.preventDefault();
        currentSearch = this.querySelector('[name="search"]').value.trim();
        currentStatus = this.querySelector('[name="status"]').value;
        currentPage = 1;
        fetchCategories();
    });

    // ── Reset button ──────────────────────────────────────────────────────────
    document.getElementById('resetBtn')?.addEventListener('click', function () {
        const form = document.getElementById('filterForm');
        if (form) {
            form.querySelector('[name="search"]').value = '';
            form.querySelector('[name="status"]').value = '';
        }
        currentSearch = '';
        currentStatus = '';
        currentPage = 1;
        fetchCategories();
    });



    // ── Check-all ─────────────────────────────────────────────────────────────
    document.getElementById('checkAll')?.addEventListener('change', function () {
        document.querySelectorAll('#categoryTableBody input[type="checkbox"]')
            .forEach(cb => cb.checked = this.checked);
    });

    // =========================================================================
    // FETCH
    // =========================================================================
    async function fetchCategories() {
        showSpinner();
        try {
            const params = new URLSearchParams({
                page: currentPage,
                search: currentSearch,
                status: currentStatus
            });
            const res = await fetch('/api/v1/admin/danh-muc?' + params, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            const json = await res.json();
            if (json.success) {
                renderCategories(json.data.items);
                renderPagination(json.data.pagination);
                renderStats(json.data.stats);
            } else {
                showToast(json.message || 'Lỗi tải dữ liệu', 'danger');
                showEmpty();
            }
        } catch (err) {
            console.error(err);
            showToast('Không thể kết nối server', 'danger');
            showEmpty();
        }
    }

    // =========================================================================
    // RENDER – TABLE
    // =========================================================================
    function renderCategories(items) {
        const tbody = document.getElementById('categoryTableBody');
        if (!tbody) return;

        if (!items || items.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="8" class="text-center py-5">
                        <i class="fas fa-tags fa-3x text-muted opacity-25 mb-3 d-block"></i>
                        <p class="text-muted mb-2">Không tìm thấy danh mục nào</p>
                        <a href="/admin/danh-muc/create" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus me-1"></i>Tạo Danh mục
                        </a>
                    </td>
                </tr>`;
            return;
        }

        tbody.innerHTML = '';
        items.forEach(c => {
            const tr = document.createElement('tr');
            
            // Image template
            const imgPath = c.HinhAnh ? c.HinhAnh : '/images/categories/default.png';
            const imgHtml = `
                <div style="width: 40px; height: 40px; border-radius: 8px; overflow: hidden; background: #f3f4f6; display: flex; align-items: center; justify-content: center; border: 1px solid var(--border); margin: auto;">
                    <img src="${imgPath}" alt="${c.TenDanhMuc}" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.src='https://placehold.co/100x100?text=${encodeURIComponent(c.TenDanhMuc)}'">
                </div>`;

            // Status template
            const statusHtml = c.TrangThai === 'Hoạt động'
                ? `<span class="badge badge-status status-active"><i class="fas fa-check-circle"></i> Hoạt động</span>`
                : `<span class="badge badge-status status-locked"><i class="fas fa-times-circle"></i> Đã khóa</span>`;

            tr.innerHTML = `
                <td class="text-center">${imgHtml}</td>
                <td>
                    <div class="fw-bold mb-0">${c.TenDanhMuc}</div>
                </td>
                <td class="fw-semibold text-secondary">${c.Slug || ''}</td>
                <td>
                    <span class="text-muted text-truncate d-inline-block" style="max-width: 320px;" title="${c.MoTa || 'Chưa có mô tả'}">
                        ${c.MoTa || '<em class="opacity-50">Chưa có mô tả</em>'}
                    </span>
                </td>
                <td class="text-center fw-bold text-primary" style="font-size: 15px;">${c.mon_an_count ?? 0}</td>
                <td class="text-center">${statusHtml}</td>
                <td class="text-center"><small class="text-muted">${fmtDate(c.created_at)}</small></td>
                <td class="text-center">
                    <div class="d-flex justify-content-center gap-1">
                        <a href="/admin/danh-muc/${c.MaDanhMuc}" class="btn-action btn-action-view" title="Xem chi tiết">
                            <i class="fas fa-eye" style="font-size:12px;"></i>
                        </a>
                        <a href="/admin/danh-muc/${c.MaDanhMuc}/edit" class="btn-action btn-action-edit" title="Sửa">
                            <i class="fas fa-edit" style="font-size:12px;"></i>
                        </a>
                        <button type="button" class="btn-action btn-action-delete"
                            onclick="openDeleteModal(${c.MaDanhMuc}, '${c.TenDanhMuc}')" title="Xóa">
                            <i class="fas fa-trash" style="font-size:12px;"></i>
                        </button>
                    </div>
                </td>`;
            tbody.appendChild(tr);
        });
    }

    // =========================================================================
    // RENDER – PAGINATION
    // =========================================================================
    function renderPagination(p) {
        const info = document.getElementById('paginationInfo');
        const list = document.getElementById('paginationList');
        if (!p) return;

        const from = p.total === 0 ? 0 : ((p.current_page - 1) * p.per_page) + 1;
        const to = Math.min(p.current_page * p.per_page, p.total);
        if (info) info.textContent = `Hiển thị ${from}–${to} trong tổng số ${p.total} danh mục`;

        if (!list) return;
        if (p.last_page <= 1) { list.innerHTML = ''; return; }

        let html = `<li class="page-item ${p.current_page === 1 ? 'disabled' : ''}">
            <a class="page-link" href="#" data-page="${p.current_page - 1}"><i class="fas fa-chevron-left" style="font-size: 11px;"></i></a></li>`;

        for (let i = 1; i <= p.last_page; i++) {
            if (i === 1 || i === p.last_page || Math.abs(i - p.current_page) <= 1) {
                html += `<li class="page-item ${i === p.current_page ? 'active' : ''}">
                    <a class="page-link" href="#" data-page="${i}">${i}</a></li>`;
            } else if (Math.abs(i - p.current_page) === 2) {
                html += `<li class="page-item disabled"><span class="page-link">…</span></li>`;
            }
        }

        html += `<li class="page-item ${p.current_page === p.last_page ? 'disabled' : ''}">
            <a class="page-link" href="#" data-page="${p.current_page + 1}"><i class="fas fa-chevron-right" style="font-size: 11px;"></i></a></li>`;

        list.innerHTML = html;
        list.querySelectorAll('.page-link[data-page]').forEach(a => {
            a.addEventListener('click', e => {
                e.preventDefault();
                const pg = parseInt(a.dataset.page);
                if (pg && pg !== currentPage) { currentPage = pg; fetchCategories(); }
            });
        });
    }

    // =========================================================================
    // RENDER – STATS
    // =========================================================================
    function renderStats(s) {
        if (!s) return;
        const set = (id, v) => { const el = document.getElementById(id); if (el) el.textContent = v; };
        set('statTotal',  s.total  ?? 0);
        set('statActive', s.active ?? 0);
        set('statTotalFoods', s.total_foods ?? 0);
        set('statAvgFoods', s.avg_foods ?? 0);
    }

    // =========================================================================
    // HELPERS
    // =========================================================================
    function fmtDate(str) {
        if (!str) return '—';
        return new Date(str).toLocaleDateString('vi-VN') + ' ' + new Date(str).toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' });
    }

    function showSpinner() {
        const tbody = document.getElementById('categoryTableBody');
        if (tbody) tbody.innerHTML = `<tr><td colspan="8" class="text-center py-4">
            <div class="spinner-border text-primary" role="status"></div></td></tr>`;
    }

    function showEmpty() {
        const tbody = document.getElementById('categoryTableBody');
        if (tbody) tbody.innerHTML = `<tr><td colspan="8" class="text-center py-4 text-muted">
            Không thể tải dữ liệu</td></tr>`;
    }

    // =========================================================================
    // DELETE
    // =========================================================================
    async function doDelete(id) {
        try {
            const res = await fetch(`/api/v1/admin/danh-muc/${id}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });
            const json = await res.json();
            if (json.success) {
                showToast('Xóa danh mục thành công!', 'success');
                fetchCategories();
            } else {
                showToast(json.message || 'Lỗi khi xóa', 'danger');
            }
        } catch (err) {
            console.error(err);
            showToast('Không thể kết nối server', 'danger');
        }
    }

    // =========================================================================
    // TOAST
    // =========================================================================
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

    // =========================================================================
    // WINDOW GLOBALS
    // =========================================================================
    window.openDeleteModal = function (id, name) {
        window.showConfirmModal(
            'Xác nhận xóa',
            `Bạn có chắc chắn muốn xóa danh mục <strong class="text-danger">${name}</strong>?<br>Hành động này không thể hoàn tác.`,
            function() {
                doDelete(id);
            }
        );
    };
});
