@extends('admin.layouts.app')

@section('title', 'Chi tiết Voucher')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center gap-2">
            <i class="fas fa-eye text-primary fs-4"></i>
            <h4 class="mb-0 fw-bold">Chi tiết Voucher</h4>
        </div>
        <a href="{{ route('admin.vouchers.index') }}" class="btn btn-light btn-sm border">
            <i class="fas fa-arrow-left me-1"></i> Quay lại
        </a>
    </div>

    <!-- Main Content Card -->
    <div class="card border-0 shadow-sm" style="border-radius: 16px; overflow: hidden;">
        <div class="card-body p-4">
            
            <!-- Big Voucher Card -->
            <div class="voucher-card mb-4" style="background: linear-gradient(135deg, #6366f1 0%, #06b6d4 100%); color: #fff; border-radius: 16px; padding: 2.5rem; text-align: center; position: relative; overflow: hidden;">
                <!-- Decorative Circles -->
                <div style="position: absolute; top: -100px; left: -100px; width: 250px; height: 250px; border-radius: 50%; background: rgba(255,255,255,0.05);"></div>
                <div style="position: absolute; bottom: -80px; right: -80px; width: 200px; height: 200px; border-radius: 50%; background: rgba(255,255,255,0.05);"></div>
                
                <div class="d-flex justify-content-center align-items-center gap-2 mb-2">
                    <i class="fas fa-ticket-alt"></i>
                    <span style="text-transform: uppercase; letter-spacing: 1px; font-size: 0.85rem; opacity: 0.9;">VOUCHER GIẢM GIÁ</span>
                </div>
                
                <div class="display-2 fw-bold mb-3" id="cardPhanTram" style="font-size: 5rem;">0%</div>
                
                <div class="mb-3">
                    <span class="badge" style="background: rgba(255, 255, 255, 0.2); backdrop-filter: blur(5px); color: #fff; font-size: 1.5rem; padding: 0.5rem 1.5rem; border-radius: 99px; letter-spacing: 1px;" id="cardMaCode">------</span>
                </div>
                
                <div class="d-flex justify-content-center align-items-center gap-2 small opacity-75">
                    <i class="far fa-calendar-alt"></i>
                    <span id="cardDateRange">--/--/---- - --/--/----</span>
                </div>
            </div>

            <!-- Detail Table -->
            <div class="detail-table-container" style="background: #111827; color: #f9fafb; border-radius: 12px; overflow: hidden;">
                <table class="table table-dark mb-0" style="--bs-table-bg: #111827; --bs-table-border-color: #374151;">
                    <thead>
                        <tr>
                            <th class="px-4 py-3 fw-normal" style="font-size: 0.85rem; color: #9ca3af;">Thông tin</th>
                            <th class="px-4 py-3 fw-normal" style="font-size: 0.85rem; color: #9ca3af;">Giá trị</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="px-4 py-3 border-secondary">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="fas fa-ticket-alt" style="color: #9ca3af;"></i>
                                    <span>Mã Voucher</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 border-secondary fw-bold" style="color: #ec4899;" id="tableMaCode">-</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-3 border-secondary">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="fas fa-percent" style="color: #9ca3af;" id="labelIcon"></i>
                                    <span id="labelPhanTramOrTienMat">Phần trăm giảm giá</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 border-secondary">
                                <span class="badge" style="background: #10b981; color: #fff;" id="tablePhanTram">0%</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="px-4 py-3 border-secondary">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="fas fa-shopping-cart" style="color: #9ca3af;"></i>
                                    <span>Đơn hàng tối thiểu</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 border-secondary" id="tableDonHangToiThieu">-</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-3 border-secondary">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="fas fa-ticket-alt" style="color: #9ca3af;"></i>
                                    <span>Tổng số lượng phát hành</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 border-secondary" id="tableSoLuongToiDa">-</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-3 border-secondary">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="fas fa-users" style="color: #9ca3af;"></i>
                                    <span>Số lần tối đa/1 User</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 border-secondary" id="tableGioiHanNguoiDung">-</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-3 border-secondary">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="fas fa-calendar-alt" style="color: #9ca3af;"></i>
                                    <span>Thời gian hiệu lực</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 border-secondary" id="tableDateRange">-</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-3 border-secondary">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="fas fa-check" style="color: #9ca3af;"></i>
                                    <span>Số ngày có hiệu lực</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 border-secondary">
                                <span class="badge" style="background: #06b6d4; color: #fff;" id="tableDuration">-</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="px-4 py-3 border-secondary">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="fas fa-info-circle" style="color: #9ca3af;"></i>
                                    <span>Mô tả</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 border-secondary" id="tableMoTa">-</td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>

<script>
    const VOUCHER_ID = {{ $id }};
</script>
<script src="{{ asset('js/api-admin-voucher-detail.js') }}"></script>

<style>
    /* Custom styles to match the dark table in Figma */
    .table-dark {
        color: #f9fafb;
    }
    .table-dark td {
        border-color: #1f2937 !important;
    }
    .badge {
        font-weight: 600;
        padding: 0.35em 0.65em;
        border-radius: 6px;
    }
</style>
@endsection
