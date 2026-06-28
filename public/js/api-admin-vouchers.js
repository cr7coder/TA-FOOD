document.addEventListener('DOMContentLoaded', function () {
    let currentPage = 1;
    let currentSearch = '';
    let currentStatus = '';
    let currentPercentFrom = '';
    let currentPercentTo = '';
    let deleteTargetId = null;

    // ── Initial load ──────────────────────────────────────────────────────────
    fetchVouchers();

    // ── Filter form ───────────────────────────────────────────────────────────
    document.getElementById('filterForm')?.addEventListener('submit', function (e) {
        e.preventDefault();
        currentSearch      = this.querySelector('[name="search"]').value.trim();
        currentStatus      = this.querySelector('[name="status"]').value;
        currentPercentFrom = this.querySelector('[name="percent_from"]').value;
        currentPercentTo   = this.querySelector('[name="percent_to"]').value;
        currentPage = 1;
        fetchVouchers();
    });

    // ── Reset button ──────────────────────────────────────────────────────────
    document.getElementById('resetBtn')?.addEventListener('click', function () {
        const form = document.getElementById('filterForm');
        form.querySelector('[name="search"]').value      = '';
        form.querySelector('[name="status"]').value      = '';
        form.querySelector('[name="percent_from"]').value = '';
        form.querySelector('[name="percent_to"]').value   = '';
        currentSearch = currentStatus = currentPercentFrom = currentPercentTo = '';
        currentPage = 1;
        fetchVouchers();
    });



    // ── Check-all ─────────────────────────────────────────────────────────────
    document.getElementById('checkAll')?.addEventListener('change', function () {
        document.querySelectorAll('#voucherTableBody input[type="checkbox"]')
            .forEach(cb => cb.checked = this.checked);
    });

    // =========================================================================
    // FETCH
    // =========================================================================
    async function fetchVouchers() {
        showSpinner();
        try {
            const params = new URLSearchParams({
                page: currentPage,
                search: currentSearch,
                status: currentStatus,
                percent_from: currentPercentFrom,
                percent_to: currentPercentTo
            });
            const res = await fetch('/api/v1/admin/vouchers?' + params, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            const json = await res.json();
            if (json.success) {
                renderVouchers(json.data.items);
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
    function renderVouchers(items) {
        const tbody = document.getElementById('voucherTableBody');
        if (!tbody) return;

        if (!items || items.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="11" class="text-center py-5">
                        <i class="fas fa-ticket-alt fa-3x text-muted opacity-25 mb-3 d-block"></i>
                        <p class="text-muted mb-2">Không tìm thấy voucher nào</p>
                        <a href="/admin/vouchers/create" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus me-1"></i>Tạo Voucher
                        </a>
                    </td>
                </tr>`;
            return;
        }

        tbody.innerHTML = '';
        items.forEach(v => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td><input type="checkbox" class="form-check-input" value="${v.MaGiamGia}"></td>
                <td>
                    <div class="d-flex align-items-center gap-1">
                        <code class="px-2 py-1 rounded fw-bold text-dark" style="font-size:12px; background:#f1f5f9; border: 1px solid #e2e8f0; font-family: 'JetBrains Mono', monospace;">${v.MaCode}</code>
                        <button class="btn btn-link btn-sm p-0 text-muted" onclick="copyCode('${v.MaCode}')" title="Copy">
                            <i class="fas fa-copy" style="font-size:11px;"></i>
                        </button>
                    </div>
                </td>
                <td class="text-center">
                    ${v.LoaiGiamGia === 'TienMat' 
                        ? `<span class="badge" style="background: rgba(16, 185, 129, 0.12); color: #059669; font-weight: 700; font-size: 0.8rem; padding: 0.4em 0.8em; border-radius: 6px;">Giảm ${Number(v.GiamToiDa).toLocaleString('vi-VN')} đ</span>`
                        : `<span class="badge" style="background: rgba(6, 182, 212, 0.12); color: #0891b2; font-weight: 700; font-size: 0.8rem; padding: 0.4em 0.8em; border-radius: 6px;">Giảm ${v.PhanTram}%</span>
                           ${v.GiamToiDa && parseFloat(v.GiamToiDa) > 0 ? `<div class="text-muted" style="font-size: 10px; margin-top: 2px;">Tối đa ${Number(v.GiamToiDa).toLocaleString('vi-VN')}đ</div>` : ''}`
                    }
                </td>
                <td class="text-center">
                    ${v.DonHangToiThieu
                        ? `<span class="badge text-muted" style="background: #f1f5f9; font-weight: 600; font-size: 0.8rem; border: 1px solid #e2e8f0; padding: 0.4em 0.8em; border-radius: 6px;">${Number(v.DonHangToiThieu).toLocaleString('vi-VN')} đ</span>`
                        : '<span class="text-muted small">—</span>'}
                </td>
                <td class="text-center">
                    <span class="badge" style="background: rgba(139, 92, 246, 0.12); color: #7c3aed; font-weight: 600; font-size: 0.8rem; padding: 0.4em 0.8em; border-radius: 6px;">
                        ${v.SoLuongDaSuDung ?? 0} / ${v.SoLuongToiDa ?? '∞'}
                    </span>
                </td>
                <td class="text-center">
                    <span class="badge" style="background: rgba(244, 63, 94, 0.12); color: #e11d48; font-weight: 600; font-size: 0.8rem; padding: 0.4em 0.8em; border-radius: 6px;">
                        ${v.GioiHanNguoiDung ?? 1} lần
                    </span>
                </td>
                <td class="text-center"><small class="text-muted">${fmtDate(v.NgayBatDau)}</small></td>
                <td class="text-center"><small class="text-muted">${fmtDate(v.NgayKetThuc)}</small></td>
                <td class="text-center">
                    <span class="time-remaining text-${remainColor(v)}">
                        <i class="fas fa-clock me-1"></i>${timeLeft(v)}
                    </span>
                </td>
                <td class="text-center">${statusBadge(v)}</td>
                <td class="text-center">
                    <div class="d-flex justify-content-center gap-1">
                        <a href="/admin/vouchers/${v.MaGiamGia}" class="btn-action btn-action-view" title="Xem">
                            <i class="fas fa-eye" style="font-size:12px;"></i>
                        </a>
                        <a href="/admin/vouchers/${v.MaGiamGia}/edit" class="btn-action btn-action-edit" title="Sửa">
                            <i class="fas fa-edit" style="font-size:12px;"></i>
                        </a>
                        <button type="button" class="btn-action btn-action-delete"
                            onclick="openDeleteModal(${v.MaGiamGia}, '${v.MaCode}')" title="Xóa">
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

        const from = ((p.current_page - 1) * p.per_page) + 1;
        const to   = Math.min(p.current_page * p.per_page, p.total);
        if (info) info.textContent = `Hiển thị ${from}–${to} trong tổng số ${p.total} voucher`;

        if (!list) return;
        if (p.last_page <= 1) { list.innerHTML = ''; return; }

        let html = `<li class="page-item ${p.current_page===1?'disabled':''}">
            <a class="page-link" href="#" data-page="${p.current_page-1}"><i class="fas fa-chevron-left" style="font-size: 11px;"></i></a></li>`;

        for (let i = 1; i <= p.last_page; i++) {
            if (i===1 || i===p.last_page || Math.abs(i-p.current_page)<=1) {
                html += `<li class="page-item ${i===p.current_page?'active':''}">
                    <a class="page-link" href="#" data-page="${i}">${i}</a></li>`;
            } else if (Math.abs(i-p.current_page)===2) {
                html += `<li class="page-item disabled"><span class="page-link">…</span></li>`;
            }
        }

        html += `<li class="page-item ${p.current_page===p.last_page?'disabled':''}">
            <a class="page-link" href="#" data-page="${p.current_page+1}"><i class="fas fa-chevron-right" style="font-size: 11px;"></i></a></li>`;

        list.innerHTML = html;
        list.querySelectorAll('.page-link[data-page]').forEach(a => {
            a.addEventListener('click', e => {
                e.preventDefault();
                const pg = parseInt(a.dataset.page);
                if (pg && pg !== currentPage) { currentPage = pg; fetchVouchers(); }
            });
        });
    }

    // =========================================================================
    // RENDER – STATS
    // =========================================================================
    function renderStats(s) {
        if (!s) return;
        const set = (id, v) => { const el = document.getElementById(id); if (el) el.textContent = v; };
        set('statTotal',       s.total        ?? 0);
        set('statActive',      s.active        ?? 0);
        set('statExpiringSoon',s.expiring_soon ?? 0);
        set('statExpired',     s.expired       ?? 0);
    }

    // =========================================================================
    // HELPERS
    // =========================================================================

    // Lấy ngày hôm nay dạng "YYYY-MM-DD" theo giờ local
    function todayStr() {
        const now = new Date();
        const y = now.getFullYear();
        const m = String(now.getMonth() + 1).padStart(2, '0');
        const d = String(now.getDate()).padStart(2, '0');
        return `${y}-${m}-${d}`;
    }

    function statusBadge(v) {
        const today = todayStr();
        const start = (v.NgayBatDau  || '').substring(0, 10);
        const end   = (v.NgayKetThuc || '').substring(0, 10);

        if (today < start) return `<span class="badge-status status-cho-xac-nhan"><i class="fas fa-clock"></i> Sắp diễn ra</span>`;
        if (today > end)   return `<span class="badge-status status-huy"><i class="fas fa-times"></i> Hết hạn</span>`;
        return `<span class="badge-status status-hoan-thanh"><i class="fas fa-check"></i> Còn hiệu lực</span>`;
    }

    function timeLeft(v) {
        const today = todayStr();
        const end   = (v.NgayKetThuc || '').substring(0, 10);
        if (today > end) return 'Hết hạn';

        // Tính số ngày còn lại
        const now  = new Date();
        const endD = new Date(end + 'T23:59:59');
        const ms   = endD - now;
        if (ms <= 0) return 'Hết hạn';
        const days  = Math.floor(ms / 86400000);
        const hours = Math.floor((ms % 86400000) / 3600000);
        if (days > 0) return `${days} ngày`;
        if (hours > 0) return `${hours} giờ`;
        return '< 1 giờ';
    }

    function remainColor(v) {
        const today = todayStr();
        const end   = (v.NgayKetThuc || '').substring(0, 10);
        if (today > end) return 'danger';

        const now  = new Date();
        const endD = new Date(end + 'T23:59:59');
        const days = Math.floor((endD - now) / 86400000);
        return days <= 3 ? 'warning' : 'success';
    }

    function fmtDate(str) {
        if (!str) return '—';
        return new Date(str).toLocaleDateString('vi-VN');
    }

    function showSpinner() {
        const tbody = document.getElementById('voucherTableBody');
        if (tbody) tbody.innerHTML = `<tr><td colspan="11" class="text-center py-4">
            <div class="spinner-border text-primary" role="status"></div></td></tr>`;
    }

    function showEmpty() {
        const tbody = document.getElementById('voucherTableBody');
        if (tbody) tbody.innerHTML = `<tr><td colspan="11" class="text-center py-4 text-muted">
            Không thể tải dữ liệu</td></tr>`;
    }

    // =========================================================================
    // DELETE
    // =========================================================================
    async function doDelete(id) {
        try {
            const res = await fetch(`/api/v1/admin/vouchers/${id}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });
            const json = await res.json();
            if (json.success) {
                showToast('Xóa voucher thành công!', 'success');
                fetchVouchers();
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
    window.openDeleteModal = function (id, code) {
        window.showConfirmModal(
            'Xác nhận xóa',
            `Bạn có chắc chắn muốn xóa voucher <strong class="text-danger">${code}</strong>?<br>Hành động này không thể hoàn tác.`,
            function() {
                doDelete(id);
            }
        );
    };

    window.copyCode = function (text) {
        navigator.clipboard.writeText(text).then(() => showToast('Đã copy: ' + text));
    };
});
