document.addEventListener('DOMContentLoaded', function() {
    if (typeof VOUCHER_ID === 'undefined') return;

    fetchVoucherDetail();

    async function fetchVoucherDetail() {
        try {
            const response = await fetch(`/api/v1/admin/vouchers/${VOUCHER_ID}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const result = await response.json();

            if (result.success) {
                const voucher = result.data;
                
                // Fill Big Card
                const cardPhanTramEl = document.getElementById('cardPhanTram');
                if (voucher.LoaiGiamGia === 'TienMat') {
                    cardPhanTramEl.textContent = Number(voucher.GiamToiDa).toLocaleString('vi-VN') + 'đ';
                    cardPhanTramEl.style.fontSize = '2.5rem';
                } else {
                    cardPhanTramEl.textContent = voucher.PhanTram + '%';
                    cardPhanTramEl.style.fontSize = '';
                }
                document.getElementById('cardMaCode').textContent = voucher.MaCode;
                document.getElementById('cardDateRange').textContent = `${formatDate(voucher.NgayBatDau)} - ${formatDate(voucher.NgayKetThuc)}`;

                // Fill Table
                document.getElementById('tableMaCode').textContent = voucher.MaCode;
                
                const labelEl = document.getElementById('labelPhanTramOrTienMat');
                const iconEl = document.getElementById('labelIcon');
                
                if (voucher.LoaiGiamGia === 'TienMat') {
                    if (iconEl) iconEl.className = 'fas fa-money-bill-wave';
                    if (labelEl) labelEl.textContent = 'Số tiền giảm';
                    document.getElementById('tablePhanTram').textContent = Number(voucher.GiamToiDa).toLocaleString('vi-VN') + ' VND';
                } else {
                    if (iconEl) iconEl.className = 'fas fa-percent';
                    if (labelEl) labelEl.textContent = 'Phần trăm giảm giá';
                    let val = voucher.PhanTram + '%';
                    if (voucher.GiamToiDa && parseFloat(voucher.GiamToiDa) > 0) {
                        val += ` (Tối đa ${Number(voucher.GiamToiDa).toLocaleString('vi-VN')} VND)`;
                    }
                    document.getElementById('tablePhanTram').textContent = val;
                }
                document.getElementById('tableDonHangToiThieu').textContent = voucher.DonHangToiThieu ? Number(voucher.DonHangToiThieu).toLocaleString('vi-VN') + ' VNĐ' : 'Không giới hạn';
                document.getElementById('tableSoLuongToiDa').textContent = voucher.SoLuongToiDa ? voucher.SoLuongToiDa + ' lượt' : 'Không giới hạn';
                document.getElementById('tableGioiHanNguoiDung').textContent = voucher.GioiHanNguoiDung ? voucher.GioiHanNguoiDung + ' lần' : 'Không giới hạn';
                document.getElementById('tableDateRange').textContent = `${formatDate(voucher.NgayBatDau)} - ${formatDate(voucher.NgayKetThuc)}`;
                
                // Calculate duration
                const start = new Date(voucher.NgayBatDau);
                const end = new Date(voucher.NgayKetThuc);
                const diffTime = Math.abs(end - start);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                document.getElementById('tableDuration').textContent = diffDays + ' ngày';
                document.getElementById('tableMoTa').textContent = voucher.MoTa || 'Không có';

            } else {
                alert(result.message || 'Lỗi khi tải chi tiết voucher');
            }
        } catch (error) {
            console.error('Error fetching voucher details:', error);
            alert('Không thể kết nối đến server');
        }
    }

    function formatDate(dateStr) {
        if (!dateStr) return '';
        const date = new Date(dateStr);
        return date.toLocaleDateString('vi-VN');
    }
});
