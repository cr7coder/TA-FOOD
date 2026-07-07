@extends('client.layouts.master')

@section('content')

    <style>
        .user-dropdown {
            padding: 8px 15px;
            border-radius: 25px;
            background: rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }

        .user-dropdown:hover {
            background: rgba(255, 255, 255, 0.2);
            text-decoration: none !important;
            color: white !important;
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #ffbe33;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 14px;
            color: #333;
        }

        /* Rating stars */
        .rating i {
            font-size: 14px;
        }

        .rating .fa-star {
            color: #ffc107;
        }

        .rating .fa-star-half-alt {
            color: #ffc107;
        }

        .rating .text-muted {
            color: #dee2e6 !important;
        }

        /* Food card hover effect */
        .box:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            transition: all 0.3s ease;
        }

        .box {
            transition: all 0.3s ease;
        }

        /* Image hover zoom effect */
        .box:hover .img-box img {
            transform: scale(1.08);
        }

        /* Bounce animation for floating cart */
        @keyframes cartBounce {
            0% { transform: scale(1); }
            30% { transform: scale(1.35); }
            50% { transform: scale(0.9); }
            70% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }

        .cart-bounce {
            animation: cartBounce 0.6s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }

        /* Load More Button Styles */
        #loadMoreBtn {
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94) !important;
        }
        #loadMoreBtn:hover {
            transform: translateY(-3px) scale(1.03) !important;
            box-shadow: 0 12px 30px rgba(255, 190, 51, 0.45) !important;
            filter: brightness(1.08);
        }
        #loadMoreBtn:active {
            transform: translateY(1px) !important;
        }

        /* Voucher Card Hover Effect */
        .voucher-card {
            transition: transform 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94), box-shadow 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94) !important;
        }
        .voucher-card:hover {
            transform: translateY(-6px) !important;
            box-shadow: 0 20px 45px rgba(0,0,0,0.3) !important;
        }

        /* Responsive Voucher Card on Mobile */
        @media (max-width: 576px) {
            .voucher-card .main-body {
                padding: 12px 10px !important;
                gap: 10px !important;
            }
            .voucher-card .promo-img {
                width: 75px !important;
                min-width: 75px !important;
                height: 75px !important;
            }
            .voucher-card .promo-pct {
                font-size: 1.6rem !important;
            }
            .voucher-card .promo-actions {
                flex-direction: column !important;
                align-items: stretch !important;
                gap: 6px !important;
            }
            .voucher-card .promo-code {
                font-size: 10.5px !important;
                padding: 4px 8px !important;
                text-align: center;
            }
            .voucher-card .pre-apply-btn {
                padding: 6px 0 !important;
                font-size: 11px !important;
                width: 100% !important;
                justify-content: center !important;
            }
        }
    </style>



    <div class="hero_area">
        <div class="bg-box">
            <img src="{{ asset('images/bg2.jpg') }}" alt="">
        </div>
        @include('client.partials._header')
        <div id="mainLocation" class="location"
            style="padding: 30px; background-color: rgba(0, 0, 0, 0.5); margin: 20px auto; width: 50%; border-radius: 20px; z-index: 999; position: relative; transition: all 0.4s ease;">

            <h4 style="color: white; text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.8); margin-bottom: 15px;">
                Nhập địa chỉ bạn muốn giao món
            </h4>
            <form class="d-flex align-items-center" style="position: relative; width: 100%; gap: 12px;">
                <div style="position: relative; flex-grow: 1;">
                    <!-- Icon Location đầu -->
                    <i class="bi bi-geo-alt-fill"
                        style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: gray; font-size: 18px; z-index: 5;"></i>

                    <!-- INPUT NHAP DIA CHI -->
                    <input id="addressInput" class="form-control rounded-pill" type="search" placeholder="Nhập địa chỉ..."
                        aria-label="Search" style="padding-left: 40px; padding-right: 45px; height: 45px; border-radius: 30px !important;">

                    <!-- Icon GPS -->
                    <i id="getLocationBtn" class="bi bi-crosshair"
                        style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); cursor: pointer; color: gray; font-size: 18px; z-index: 5;" title="Tự động định vị GPS"></i>
                </div>
                <button type="button" id="syncAddressBtn" class="btn btn-warning text-dark font-weight-bold rounded-pill custom-sync-btn d-flex align-items-center justify-content-center" style="background: #ffbe33; border-color: #ffbe33; height: 45px; font-size: 14px; white-space: nowrap; box-shadow: 0 4px 10px rgba(255, 190, 51, 0.3); transition: all 0.2s;" onmouseover="this.style.transform='translateY(-1px)';" onmouseout="this.style.transform='none';">
                    <i class="fa fa-search-location"></i> <span class="btn-text" style="margin-left: 6px;">Tìm vị trí</span>
                </button>
            </form>

            <!-- Address Detail Panel (Hidden by default) -->
            <div id="addressDetailPanel" class="mt-3 p-3 text-left d-none animate__animated animate__fadeIn" style="background: rgba(255, 255, 255, 0.15); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px); border-radius: 16px; border: 1px solid rgba(255, 255, 255, 0.25); color: white; font-size: 13px; transition: all 0.3s ease;">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="font-weight-bold text-warning" style="letter-spacing: 0.5px;"><i class="fa fa-map-marked-alt mr-1"></i> ĐÃ XÁC ĐỊNH VỊ TRÍ GIAO MÓN!</span>
                    <div class="d-flex align-items-center" style="gap: 8px;">
                        <span class="badge badge-success" style="font-size: 9px; border-radius: 12px; padding: 4px 10px; background-color: #28a745; font-weight: 700; box-shadow: 0 2px 5px rgba(40,167,69,0.3);">GPS Active</span>
                        <!-- Nút đóng panel chi tiết -->
                        <button id="closeDetailPanel" type="button"
                            style="background: rgba(255,255,255,0.15); border: none; color: #fff; width: 24px; height: 24px; border-radius: 50%; font-size: 14px; line-height: 1; cursor: pointer; display: flex; align-items: center; justify-content: center; flex-shrink: 0; transition: background 0.2s;"
                            title="Thu gọn" onmouseover="this.style.background='rgba(255,80,80,0.55)'" onmouseout="this.style.background='rgba(255,255,255,0.15)'">
                            &times;
                        </button>
                    </div>
                </div>
                <div class="mb-2 text-white-50" style="font-size: 11.5px; line-height: 1.4;">
                    Tọa độ GPS: <span id="gpsCoords" class="text-white font-weight-bold">--</span> | Bán kính phục vụ: <span class="text-white font-weight-bold">Tối ưu 3km</span>
                </div>
                
                <!-- Quick address tags -->
                <div class="d-flex align-items-center mb-2" style="gap: 8px; flex-wrap: wrap;">
                    <span class="text-white-50" style="font-size: 11.5px;">Lưu làm địa chỉ:</span>
                    <button type="button" class="btn btn-xs rounded-pill address-tag-btn" data-tag="Nhà riêng" style="padding: 3px 12px; font-size: 11px; border: 1px solid rgba(255,255,255,0.4); background: rgba(255,255,255,0.1); color: white; transition: all 0.2s; font-weight: 600; outline: none;">🏠 Nhà riêng</button>
                    <button type="button" class="btn btn-xs rounded-pill address-tag-btn" data-tag="Văn phòng" style="padding: 3px 12px; font-size: 11px; border: 1px solid rgba(255,255,255,0.4); background: rgba(255,255,255,0.1); color: white; transition: all 0.2s; font-weight: 600; outline: none;">🏢 Văn phòng</button>
                    <button type="button" class="btn btn-xs rounded-pill address-tag-btn" data-tag="Trường học" style="padding: 3px 12px; font-size: 11px; border: 1px solid rgba(255,255,255,0.4); background: rgba(255,255,255,0.1); color: white; transition: all 0.2s; font-weight: 600; outline: none;">🏫 Trường học</button>
                    <button type="button" class="btn btn-xs rounded-pill save-default-address-btn" style="padding: 3px 12px; font-size: 11px; border: 1px solid #ffbe33; background: rgba(255, 190, 51, 0.2); color: #ffbe33; transition: all 0.2s; font-weight: 700; outline: none; margin-left: auto;">⭐ Đặt làm mặc định</button>
                </div>
                
                <!-- Interactive Leaflet Mini Map -->
                <div id="miniMap" style="height: 180px; width: 100%; border-radius: 12px; margin: 12px 0; box-shadow: 0 4px 15px rgba(0,0,0,0.15); border: 1px solid rgba(255,255,255,0.25); z-index: 10;"></div>
                
                <div class="pt-2 mt-2 border-top" style="border-top-color: rgba(255, 255, 255, 0.15) !important; font-size: 11.5px; display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <i class="fa fa-shipping-fast text-warning mr-1"></i> Ship nhanh: <span class="text-white font-weight-bold">15 - 25 phút</span>
                    </div>
                    <div>
                        <i class="fa fa-dollar-sign text-warning mr-1"></i> Phí ship: <span class="text-warning font-weight-bold">Freeship (<2km)</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <section class="offer_section layout_padding-bottom">
        <div class="offer_container">
            <div class="container">
                <div class="heading_container" style="margin-bottom: 25px !important; margin-top: 10px !important; width: 100%;">
                    <h2 class="mb-0 text-left" style="text-align: left; margin: 0;">Khuyến mãi độc quyền</h2>
                </div>
                <div class="row">
                    @forelse($promotions as $index => $promo)
                        @php
                            $daysLeft = (int) ceil(now()->diffInDays($promo->NgayKetThuc, false));
                            $urgencyColor = $daysLeft <= 2 ? '#ef4444' : ($daysLeft <= 5 ? '#f59e0b' : '#22c55e');
                            $urgencyIcon  = $daysLeft <= 2 ? '🔥' : ($daysLeft <= 5 ? '⚡' : '🗓️');
                            $usedPct = ($promo->SoLuongToiDa && $promo->SoLuongToiDa > 0)
                                  ? min(100, round(($promo->SoLuongDaSuDung / $promo->SoLuongToiDa) * 100))
                                  : null;
                        @endphp
                        <div class="col-md-6 d-flex align-items-stretch" style="margin-top: 15px !important; margin-bottom: 15px !important;">
                            <div class="voucher-card" style="width:100%; border-radius:14px; border:1px solid rgba(255,190,51,0.18); box-shadow:0 8px 25px rgba(0,0,0,0.15); display:flex; flex-direction:column; background:#222831; color:#fff; overflow:hidden; transition:transform .3s, box-shadow .3s; position:relative;">

                                {{-- Top ribbon: urgency --}}
                                <div style="background:linear-gradient(90deg, {{$urgencyColor}}18, transparent); border-bottom:1px solid {{$urgencyColor}}33; padding:5px 14px; display:flex; justify-content:space-between; align-items:center; font-size:11.5px; flex-wrap:wrap; gap:6px;">
                                    <span style="color:{{$urgencyColor}}; font-weight:700;">
                                        {{ $urgencyIcon }}
                                        @if($daysLeft <= 0)
                                            Hết hạn hôm nay!
                                        @elseif($daysLeft == 1)
                                            Còn 1 ngày — Dùng ngay!
                                        @else
                                            Còn {{ $daysLeft }} ngày
                                        @endif
                                    </span>
                                    @if($promo->DonHangToiThieu > 0)
                                        <span style="color:#94a3b8; font-size:11px;">
                                            Đơn từ {{ number_format($promo->DonHangToiThieu, 0, ',', '.') }}đ
                                        </span>
                                    @endif
                                </div>

                                {{-- Main body --}}
                                <div class="main-body" style="display:flex; align-items:center; padding:14px 14px; gap:14px; flex:1;">
                                    <div class="promo-img" style="width:110px; min-width:110px; height:110px; border-radius:50%; border:3px solid #ffbe33; overflow:hidden; box-shadow:0 0 12px rgba(255,190,51,0.25); flex-shrink:0;">
                                        <img src="{{ asset('images/o' . (($index % 2) + 1) . '.jpg') }}" alt="" style="width:100%; height:100%; object-fit:cover; object-position:center; transition:transform .4s ease;">
                                    </div>
                                    <div style="flex:1; min-width:0;">
                                        <div style="font-family:'Dancing Script',cursive; font-size:17px; margin-bottom:2px; line-height:1.25; color:#e2e8f0; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                            {{ $promo->MoTa ?? 'Khuyến mãi đặc biệt' }}
                                        </div>
                                        <div style="margin-bottom:4px; display:flex; align-items:baseline; gap:4px;">
                                            <span class="promo-pct" style="font-size:2.1rem; font-weight:900; background:linear-gradient(135deg,#ffbe33,#ffffff); -webkit-background-clip:text; -webkit-text-fill-color:transparent; font-family:'Dancing Script',cursive; line-height:1;">
                                                {{ $promo->PhanTramFormat }}
                                            </span>
                                            <span style="font-size:0.9rem; color:#cbd5e1;">Off</span>
                                        </div>

                                        {{-- Max discount info --}}
                                        @if($promo->GiamToiDa > 0)
                                            <div style="font-size:11px; color:#94a3b8; margin-bottom:6px;">
                                                Giảm tối đa {{ number_format($promo->GiamToiDa, 0, ',', '.') }}đ
                                            </div>
                                        @else
                                            <div style="font-size:11px; color:transparent; margin-bottom:6px; user-select:none;">
                                                &nbsp;
                                            </div>
                                        @endif

                                        {{-- Code with click-to-copy & button side by side --}}
                                        <div class="promo-actions" style="display:flex; align-items:center; justify-content:space-between; gap:10px; margin-top:8px;">
                                            <div class="promo-code" style="background:rgba(255,190,51,0.1); border:1px dashed rgba(255,190,51,0.5); color:#ffbe33; font-weight:800; font-size:11.5px; padding:4px 10px; border-radius:6px; letter-spacing:1px; flex-shrink:0; user-select:all;">
                                                Mã: {{ $promo->MaCode }}
                                            </div>

                                            <a href="javascript:void(0)" class="pre-apply-btn" data-code="{{ $promo->MaCode }}"
                                                style="display:inline-flex; align-items:center; justify-content:center; gap:6px; border-radius:20px; padding:6px 16px; background:linear-gradient(135deg,#ffbe33,#ff9800); color:#222831; font-weight:700; font-size:12px; text-decoration:none; transition:all .3s; border:none; box-shadow:0 3px 8px rgba(255,190,51,.25); flex-shrink:0;">
                                                <i class="fa fa-ticket-alt" style="font-size:11px;"></i> Lưu mã
                                            </a>
                                        </div>

                                        {{-- Usage progress bar --}}
                                        @if($usedPct !== null)
                                            <div style="margin-top:8px;">
                                                <div style="display:flex; justify-content:space-between; font-size:10px; color:#94a3b8; margin-bottom:2px;">
                                                    <span>Đã dùng {{ $promo->SoLuongDaSuDung }}/{{ $promo->SoLuongToiDa }} lượt</span>
                                                    <span style="color:{{ $usedPct >= 80 ? '#ef4444' : '#94a3b8' }}; font-weight:{{ $usedPct >= 80 ? '700' : '400' }};">
                                                        {{ $usedPct >= 80 ? '🔥 Sắp hết!' : 'Còn ' . ($promo->SoLuongToiDa - $promo->SoLuongDaSuDung) . ' lượt' }}
                                                    </span>
                                                </div>
                                                <div style="background:rgba(255,255,255,0.1); border-radius:99px; height:4px; overflow:hidden;">
                                                    <div style="height:100%; width:{{ $usedPct }}%; border-radius:99px; background:{{ $usedPct >= 80 ? 'linear-gradient(90deg,#ef4444,#f97316)' : 'linear-gradient(90deg,#ffbe33,#22c55e)' }}; transition:width .5s ease;"></div>
                                                </div>
                                            </div>
                                        @else
                                            <div style="margin-top:8px;">
                                                <div style="display:flex; justify-content:space-between; font-size:10px; color:#94a3b8; margin-bottom:2px;">
                                                    <span>Lượt dùng: Không giới hạn</span>
                                                    <span style="color:#22c55e; font-weight:700;">🟢 Khả dụng</span>
                                                </div>
                                                <div style="background:rgba(255,255,255,0.1); border-radius:99px; height:4px; overflow:hidden;">
                                                    <div style="height:100%; width:100%; border-radius:99px; background:linear-gradient(90deg,#ffbe33,#22c55e); transition:width .5s ease;"></div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-md-6">
                            <div class="box">
                                <div class="img-box">
                                    <img src="{{ asset('images/o1.jpg') }}" alt="">
                                </div>
                                <div class="detail-box">
                                    <h5>
                                        Ngày mới
                                    </h5>
                                    <h6>
                                        <span>20%</span> Off
                                    </h6>
                                    <a href="">
                                        Mua ngay <svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg"
                                            xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                                            viewBox="0 0 456.029 456.029" style="enable-background:new 0 0 456.029 456.029;"
                                            xml:space="preserve">
                                            <g>
                                                <g>
                                                    <path
                                                        d="M345.6,338.862c-29.184,0-53.248,23.552-53.248,53.248c0,29.184,23.552,53.248,53.248,53.248
                                                                                                                                                                                                                       c29.184,0,53.248-23.552,53.248-53.248C398.336,362.926,374.784,338.862,345.6,338.862z" />
                                                </g>
                                            </g>
                                            <g>
                                                <g>
                                                    <path
                                                        d="M439.296,84.91c-1.024,0-2.56-0.512-4.096-0.512H112.64l-5.12-34.304C104.448,27.566,84.992,10.67,61.952,10.67H20.48
                                                                                                                                                                                                                       C9.216,10.67,0,19.886,0,31.15c0,11.264,9.216,20.48,20.48,20.48h41.472c2.56,0,4.608,2.048,5.12,4.608l31.744,216.064
                                                                                                                                                                                                                       c4.096,27.136,27.648,47.616,55.296,47.616h212.992c26.624,0,49.664-18.944,55.296-45.056l33.28-166.4
                                                                                                                                                                                                                       C457.728,97.71,450.56,86.958,439.296,84.91z" />
                                                </g>
                                            </g>
                                            <g>
                                                <g>
                                                    <path
                                                        d="M215.04,389.55c-1.024-28.16-24.576-50.688-52.736-50.688c-29.696,1.536-52.224,26.112-51.2,55.296
                                                                                                                                                                                                                       c1.024,28.16,24.064,50.688,52.224,50.688h1.024C193.536,443.31,216.576,418.734,215.04,389.55z" />
                                                </g>
                                            </g>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="box">
                                <div class="img-box">
                                    <img src="{{ asset('images/o2.jpg') }}" alt="">
                                </div>
                                <div class="detail-box">
                                    <h5>
                                        Pizza Days
                                    </h5>
                                    <h6>
                                        <span>15%</span> Off
                                    </h6>
                                    <a href="">
                                        Mua ngay <svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg"
                                            xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                                            viewBox="0 0 456.029 456.029" style="enable-background:new 0 0 456.029 456.029;"
                                            xml:space="preserve">
                                            <g>
                                                <g>
                                                    <path
                                                        d="M345.6,338.862c-29.184,0-53.248,23.552-53.248,53.248c0,29.184,23.552,53.248,53.248,53.248
                                                                                                                                                                                                                       c29.184,0,53.248-23.552,53.248-53.248C398.336,362.926,374.784,338.862,345.6,338.862z" />
                                                </g>
                                            </g>
                                            <g>
                                                <g>
                                                    <path
                                                        d="M439.296,84.91c-1.024,0-2.56-0.512-4.096-0.512H112.64l-5.12-34.304C104.448,27.566,84.992,10.67,61.952,10.67H20.48
                                                                                                                                                                                                                       C9.216,10.67,0,19.886,0,31.15c0,11.264,9.216,20.48,20.48,20.48h41.472c2.56,0,4.608,2.048,5.12,4.608l31.744,216.064
                                                                                                                                                                                                                       c4.096,27.136,27.648,47.616,55.296,47.616h212.992c26.624,0,49.664-18.944,55.296-45.056l33.28-166.4
                                                                                                                                                                                                                       C457.728,97.71,450.56,86.958,439.296,84.91z" />
                                                </g>
                                            </g>
                                            <g>
                                                <g>
                                                    <path
                                                        d="M215.04,389.55c-1.024-28.16-24.576-50.688-52.736-50.688c-29.696,1.536-52.224,26.112-51.2,55.296
                                                                                                                                                                                                                       c1.024,28.16,24.064,50.688,52.224,50.688h1.024C193.536,443.31,216.576,418.734,215.04,389.55z" />
                                                </g>
                                            </g>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforelse
                </div>

                <!-- Sleek & Premium "Xem thêm" Button Container for Vouchers -->
                <div class="text-center mt-5" id="loadMoreVouchersContainer" style="display: block;">
                    <button id="loadMoreVouchersBtn" class="btn px-5 py-3 rounded-pill font-weight-bold" 
                            style="background: linear-gradient(135deg, #ffbe33, #ff9800); color: #222831; border: none; font-size: 14.5px; letter-spacing: 0.5px; box-shadow: 0 8px 25px rgba(255, 190, 51, 0.25); transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94); outline: none;" 
                            onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 12px 30px rgba(255, 190, 51, 0.35)';" 
                            onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 8px 25px rgba(255, 190, 51, 0.25)';"
                            data-toggle="modal" data-target="#allVouchersModal">
                        <span><i class="fa fa-ticket-alt mr-2"></i> Xem thêm khuyến mãi</span>
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- end offer section -->
    <!-- end offer section -->
    <!-- danh sách món ăn -->
    <!-- food section -->
    <section id="food-menu-section" class="food_section layout_padding-bottom">
        <div class="container">
            <div class="heading_container heading_center">
                <h2>Món ngon gần bạn</h2>
            </div>

            {{-- Menu filter theo Loại Món --}}
            <ul class="filters_menu">
                <li class="active" data-filter="*">Tất cả</li>
                @if(isset($categories) && count($categories) > 0)
                    @foreach($categories->take(7) as $c)
                        <li data-filter=".{{ \Illuminate\Support\Str::slug($c->TenDanhMuc, '-') }}">{{ $c->TenDanhMuc }}</li>
                    @endforeach
                    @if(count($categories) > 7)
                        <li class="see-all-categories-btn" style="background: rgba(255, 190, 51, 0.12) !important; color: #ffbe33 !important; font-weight: 700 !important; border: 1.5px dashed #ffbe33 !important; border-radius: 30px !important; display: inline-flex !important; align-items: center; gap: 4px; padding: 8px 20px !important;" data-toggle="modal" data-target="#allCategoriesModal">
                            <i class="fa fa-th-large"></i> Xem tất cả
                        </li>
                    @endif
                @else
                    <li data-filter=".com">Cơm</li>
                    <li data-filter=".bun">Bún</li>
                    <li data-filter=".pho">Phở</li>
                    <li data-filter=".mi">Mì</li>
                    <li data-filter=".tra-sua">Trà sữa</li>
                    <li data-filter=".banh-mi">Bánh mì</li>
                    <li data-filter=".do-chay">Đồ chay</li>
                    <li class="see-all-categories-btn" style="background: rgba(255, 190, 51, 0.12) !important; color: #ffbe33 !important; font-weight: 700 !important; border: 1.5px dashed #ffbe33 !important; border-radius: 30px !important; display: inline-flex !important; align-items: center; gap: 4px; padding: 8px 20px !important;" data-toggle="modal" data-target="#allCategoriesModal">
                        <i class="fa fa-th-large"></i> Xem tất cả
                    </li>
                @endif
            </ul>


            <!-- Advanced Premium Filter Toolbar (Dark Theme) -->
            <div class="filter-toolbar mb-4 animate__animated animate__fadeIn" style="background: #222831; border-radius: 12px; padding: 15px 25px; display: flex; flex-wrap: wrap; box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15); border-left: 5px solid #ffbe33;">
                
                <!-- Mobile Toggle Header -->
                <div class="d-flex d-md-none justify-content-between align-items-center w-100 mobile-filter-toggle" data-toggle="collapse" data-target="#mobileFilterCollapse" aria-expanded="false" style="cursor: pointer;">
                    <span class="text-white font-weight-bold" style="font-size: 15px; letter-spacing: 0.5px; text-transform: uppercase;">
                        <i class="fa fa-filter text-warning mr-2"></i>Bộ Lọc Nâng Cao
                    </span>
                    <button class="btn btn-sm p-0 text-white" style="background: transparent; border: none; box-shadow: none;">
                        <i class="fa fa-chevron-down toggle-icon" style="transition: transform 0.3s;"></i>
                    </button>
                </div>

                <!-- Collapsible Content -->
                <div class="collapse d-md-flex w-100 justify-content-between align-items-center mt-3 mt-md-0" id="mobileFilterCollapse">
                    
                    <div class="d-flex flex-wrap align-items-center gap-3 filter-group-left" style="gap: 15px;">
                        <span class="text-white font-weight-bold mr-2 filter-text-label d-none d-md-inline" style="font-size: 15px; letter-spacing: 0.5px; text-transform: uppercase;"><i class="fa fa-filter text-warning mr-2"></i>Bộ Lọc</span>
                        
                        <!-- Mức giá Dropdown -->
                        <div class="dropdown custom-filter-dropdown">
                            <button class="btn dropdown-toggle rounded-pill filter-btn" type="button" id="priceDropdownBtn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.1); font-size: 14px; font-weight: 500; color: #ffffff; padding: 8px 22px; box-shadow: none; transition: all 0.3s;">
                                <i class="fa fa-wallet text-warning mr-1"></i> <span class="filter-text">Mức giá</span>
                            </button>
                            <div class="dropdown-menu shadow-lg border-0" aria-labelledby="priceDropdownBtn" style="background: #2d343f; border-radius: 12px; overflow: hidden; font-size: 14px; min-width: 220px; margin-top: 10px; border: 1px solid rgba(255,255,255,0.05);">
                                <input type="hidden" id="priceFilter" value="">
                                <a class="dropdown-item filter-option active" href="#" data-target="#priceFilter" data-value="" data-text="Mức giá">Tất cả mức giá</a>
                                <a class="dropdown-item filter-option" href="#" data-target="#priceFilter" data-value="0-30000" data-text="Dưới 30k">Dưới 30.000đ</a>
                                <a class="dropdown-item filter-option" href="#" data-target="#priceFilter" data-value="30000-50000" data-text="30k - 50k">30.000đ - 50.000đ</a>
                                <a class="dropdown-item filter-option" href="#" data-target="#priceFilter" data-value="50000-100000" data-text="50k - 100k">50.000đ - 100.000đ</a>
                                <a class="dropdown-item filter-option" href="#" data-target="#priceFilter" data-value="100000-" data-text="Trên 100k">Trên 100.000đ</a>
                            </div>
                        </div>

                        <!-- Đánh giá Dropdown -->
                        <div class="dropdown custom-filter-dropdown">
                            <button class="btn dropdown-toggle rounded-pill filter-btn" type="button" id="ratingDropdownBtn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.1); font-size: 14px; font-weight: 500; color: #ffffff; padding: 8px 22px; box-shadow: none; transition: all 0.3s;">
                                <i class="fa fa-star text-warning mr-1"></i> <span class="filter-text">Đánh giá</span>
                            </button>
                            <div class="dropdown-menu shadow-lg border-0" aria-labelledby="ratingDropdownBtn" style="background: #2d343f; border-radius: 12px; overflow: hidden; font-size: 14px; min-width: 200px; margin-top: 10px; border: 1px solid rgba(255,255,255,0.05);">
                                <input type="hidden" id="ratingFilter" value="">
                                <a class="dropdown-item filter-option active" href="#" data-target="#ratingFilter" data-value="" data-text="Đánh giá">Mọi đánh giá</a>
                                <a class="dropdown-item filter-option" href="#" data-target="#ratingFilter" data-value="5" data-text="5 sao">5 sao <i class="fa fa-star text-warning ml-1"></i></a>
                                <a class="dropdown-item filter-option" href="#" data-target="#ratingFilter" data-value="4" data-text="Từ 4 sao">Từ 4 sao trở lên <i class="fa fa-star text-warning ml-1"></i></a>
                                <a class="dropdown-item filter-option" href="#" data-target="#ratingFilter" data-value="3" data-text="Từ 3 sao">Từ 3 sao trở lên <i class="fa fa-star text-warning ml-1"></i></a>
                                <a class="dropdown-item filter-option" href="#" data-target="#ratingFilter" data-value="2" data-text="Từ 2 sao">Từ 2 sao trở lên <i class="fa fa-star text-warning ml-1"></i></a>
                                <a class="dropdown-item filter-option" href="#" data-target="#ratingFilter" data-value="1" data-text="Từ 1 sao">Từ 1 sao trở lên <i class="fa fa-star text-warning ml-1"></i></a>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-3 filter-group-right mt-3 mt-md-0" style="gap: 15px;">
                        <!-- Sắp xếp Dropdown -->
                        <div class="dropdown custom-filter-dropdown w-100 w-md-auto">
                            <button class="btn dropdown-toggle rounded-pill filter-btn w-100" type="button" id="sortDropdownBtn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="background: #ffbe33; border: 1px solid #ffbe33; font-size: 14px; font-weight: 600; color: #222831; padding: 8px 24px; box-shadow: 0 4px 10px rgba(255, 190, 51, 0.3); transition: all 0.3s;">
                                <i class="fa fa-sort-amount-down mr-1"></i> Sắp xếp: <span class="filter-text">Bán chạy</span>
                            </button>
                            <div class="dropdown-menu dropdown-menu-right shadow-lg border-0 w-100" aria-labelledby="sortDropdownBtn" style="background: #2d343f; border-radius: 12px; overflow: hidden; font-size: 14px; min-width: 240px; margin-top: 10px; border: 1px solid rgba(255,255,255,0.05);">
                                <input type="hidden" id="sortFilter" value="best_seller">
                                <a class="dropdown-item filter-option active" href="#" data-target="#sortFilter" data-value="best_seller" data-text="Bán chạy"><i class="fa fa-fire text-danger mr-2" style="width: 18px; text-align: center;"></i> Bán chạy nhất</a>
                                <a class="dropdown-item filter-option" href="#" data-target="#sortFilter" data-value="top_rated" data-text="Đánh giá cao"><i class="fa fa-star text-warning mr-2" style="width: 18px; text-align: center;"></i> Đánh giá cao nhất</a>
                                <a class="dropdown-item filter-option" href="#" data-target="#sortFilter" data-value="price_asc" data-text="Giá tăng dần"><i class="fa fa-arrow-up text-success mr-2" style="width: 18px; text-align: center;"></i> Giá: Thấp đến Cao</a>
                                <a class="dropdown-item filter-option" href="#" data-target="#sortFilter" data-value="price_desc" data-text="Giá giảm dần"><i class="fa fa-arrow-down text-danger mr-2" style="width: 18px; text-align: center;"></i> Giá: Cao đến Thấp</a>
                                <a class="dropdown-item filter-option" href="#" data-target="#sortFilter" data-value="new" data-text="Mới nhất"><i class="fa fa-clock text-info mr-2" style="width: 18px; text-align: center;"></i> Mới nhất</a>
                            </div>
                        </div>
                        
                        <button id="resetFiltersBtn" class="btn btn-sm rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="height: 42px; width: 42px; background: rgba(255,255,255,0.1); border: none; color: #ffffff; transition: all 0.3s;" title="Xóa bộ lọc" onmouseover="this.style.background='#ffbe33'; this.style.color='#222831'; this.style.transform='rotate(180deg)';" onmouseout="this.style.background='rgba(255,255,255,0.1)'; this.style.color='#ffffff'; this.style.transform='rotate(0deg)';">
                            <i class="fa fa-sync-alt"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Loading Spinner -->
            <div id="loadingSpinner" class="text-center py-5" style="display: none;">
                <div class="spinner-border text-warning" style="width: 3rem; height: 3rem;" role="status">
                    <span class="sr-only">Đang tải...</span>
                </div>
                <p class="mt-3 text-muted">Đang tìm kiếm...</p>
            </div>

            <!-- Error Message -->
            <div id="errorMessage" class="alert alert-danger text-center" style="display: none;">
                <i class="fa fa-exclamation-circle"></i> 
                <span id="errorText"></span>
                <button class="btn btn-warning mt-2" onclick="loadFoods(1, currentFilters)">Thử lại</button>
            </div>
            <!-- xem chi tiet mon an -->
            <div class="filters-content">
                <div class="row grid">
                    @foreach($foods as $food)
                        <div class="col-6 col-sm-6 col-lg-4 all {{ $food->loai_mon_class }} {{ $food->TrangThai == 'Còn bán' ? 'con-ban' : 'ngung-ban' }}">
                            <div class="box" data-detail-url="{{ route('food.detail', $food->MaMonAn) }}" style="cursor: pointer;">
                                <div>
                                    <div class="img-box position-relative">
                                        <img src="{{ $food->hinh_anh_url }}"
                                            alt="{{ $food->TenMonAn }}" loading="lazy" style="transition: transform 0.5s cubic-bezier(0.25, 0.46, 0.45, 0.94) !important;">
                                        
                                        <!-- Heart Favorite Toggle Button -->
                                        @auth
                                            <button type="button" class="heart-fav-btn toggle-fav-btn" 
                                                    data-id="{{ $food->MaMonAn }}" 
                                                    title="{{ $food->is_favorite ? 'Xóa khỏi yêu thích' : 'Thêm vào yêu thích' }}"
                                                    style="position: absolute; top: 15px; right: 15px; width: 36px; height: 36px; background: rgba(255, 255, 255, 0.9); border: none; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; z-index: 10; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15); transition: all 0.2s ease;">
                                                <i class="{{ $food->is_favorite ? 'fa fa-heart text-danger' : 'far fa-heart text-muted' }}" style="font-size: 16px; transition: transform 0.2s ease;"></i>
                                            </button>
                                        @else
                                            <button type="button" class="heart-fav-btn require-login-fav-btn" 
                                                    title="Đăng nhập để lưu yêu thích"
                                                    style="position: absolute; top: 15px; right: 15px; width: 36px; height: 36px; background: rgba(255, 255, 255, 0.9); border: none; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; z-index: 10; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15); transition: all 0.2s ease;">
                                                <i class="far fa-heart text-muted" style="font-size: 16px;"></i>
                                            </button>
                                        @endauth

                                        {{-- Popular Star Badge --}}
                                        @if($food->diem_trung_binh >= 4.5 && $food->tong_binh_luan >= 1)
                                            <span class="badge position-absolute" style="top: 15px; left: 15px; z-index: 10; background: linear-gradient(135deg, #ffbe33, #ff9800); color: #222831; font-weight: 800; padding: 6px 12px; border-radius: 20px; box-shadow: 0 4px 10px rgba(255, 190, 51, 0.4); font-size: 11px; border: none;">
                                                <i class="fa fa-star"></i> {{ number_format($food->diem_trung_binh, 1) }} Yêu thích
                                            </span>
                                        @endif
                                    </div>
                                    <div class="detail-box">
                                        <h5>
                                            <a href="{{ route('food.detail', $food->MaMonAn) }}"
                                                class="text-decoration-none text-light">
                                                {{ $food->TenMonAn }}
                                            </a>
                                        </h5>
                                        @if($food->nhaHang)
                                            <div class="restaurant-name mb-2" style="font-size:12.5px; color: #ffbe33; font-weight: 500;">
                                                <i class="fa fa-store mr-1"></i> {{ $food->nhaHang->TenNhaHang }}
                                            </div>
                                        @endif
                                        @if($food->MoTa)
                                            <p>{{ \Illuminate\Support\Str::limit($food->MoTa, 20, '...') }}</p>
                                        @endif
                                        <div class="rating mb-2">
                                            @for($i = 1; $i <= 5; $i++)
                                                @if($i <= floor($food->diem_trung_binh))
                                                    <i class="fa fa-star text-warning"></i>
                                                @elseif($i == ceil($food->diem_trung_binh) && $food->diem_trung_binh - floor($food->diem_trung_binh) >= 0.5)
                                                    <i class="fa fa-star-half-alt text-warning"></i>
                                                @else
                                                    <i class="fa fa-star text-muted"></i>
                                                @endif
                                            @endfor
                                            <small class="text-muted ms-1">({{ $food->tong_binh_luan }})</small>
                                        </div>
                                        <div class="options">
                                            <h6>{{ number_format($food->Gia, 0, ',', '.') }} đ</h6>
                                            <div class="d-flex gap-3 align-items-center">
                                                <!-- mat xem chi -->
                                                <a href="{{ route('food.detail', $food->MaMonAn) }}"
                                                    class="btn btn-warning rounded-circle shadow-sm"
                                                    style="width: 45px; height: 45px; min-width: 45px; min-height: 45px; max-width: 45px; max-height: 45px; padding: 0; display: flex !important; align-items: center !important; justify-content: center !important; flex-shrink: 0; transition: all 0.3s ease;"
                                                    onmouseover="this.style.transform='scale(1.1)'; this.style.boxShadow='0 4px 12px rgba(255, 193, 7, 0.4)';"
                                                    onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='0 2px 6px rgba(0,0,0,0.1)';"
                                                    title="Xem chi tiết">
                                                    <i class="fa fa-eye" style="margin: 0; line-height: 1;"></i>
                                                </a>
                                                @if($food->TrangThai == 'Còn bán')
                                                    <button type="button"
                                                        class="btn btn-warning rounded-circle shadow-sm add-to-cart-btn"
                                                        style="width: 45px; height: 45px; min-width: 45px; min-height: 45px; max-width: 45px; max-height: 45px; padding: 0; display: flex !important; align-items: center !important; justify-content: center !important; flex-shrink: 0; transition: all 0.3s ease;"
                                                        data-id="{{ $food->MaMonAn }}"
                                                        data-name="{{ $food->TenMonAn }}" 
                                                        data-price="{{ $food->Gia }}"
                                                        data-image="{{ $food->hinh_anh_url }}"
                                                        onmouseover="this.style.transform='scale(1.1)'; this.style.boxShadow='0 4px 12px rgba(255, 193, 7, 0.4)';"
                                                        onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='0 2px 6px rgba(0,0,0,0.1)';"
                                                        title="Thêm vào giỏ">
                                                        <i class="fa fa-shopping-cart" style="margin: 0; line-height: 1;"></i>
                                                    </button>
                                                @else
                                                    <span class="badge bg-danger">Ngừng bán</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <!-- Sleek & Premium "Xem thêm" Button Container -->
                <div class="text-center mt-5" id="loadMoreContainer" style="display: block;">
                    <button id="loadMoreBtn" class="btn px-5 py-3 rounded-pill font-weight-bold" style="background: linear-gradient(135deg, #ffbe33, #ff9800); color: #222831; border: none; font-size: 14.5px; letter-spacing: 0.5px; box-shadow: 0 8px 25px rgba(255, 190, 51, 0.25); transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94); outline: none;">
                        <span id="loadMoreText"><i class="fa fa-utensils mr-2"></i> Xem thêm món ngon</span>
                        <span id="loadMoreSpinner" class="spinner-border spinner-border-sm d-none ml-2" role="status" style="width: 1rem; height: 1rem; color: #222831;"></span>
                    </button>
                </div>
                
                <!-- Pagination Container for Search Results (kept hidden to prevent JS error or for fallback) -->
                <div class="pagination-container mt-4 d-none"></div>
            </div>
        </div>
    </section>
    <!-- end food section -->

    <section class="restaurant_section layout_padding-bottom">
        <div class="container">
            <div class="heading_container heading_center mb-4">
                <h2>Quán ngon quanh đây</h2>
            </div>

            <div class="row">
                @forelse($restaurants as $res)
                    <div class="col-sm-6 col-lg-4 mb-4" style="--delay: {{ $loop->index * 80 }}ms;">
                        <div class="card restaurant-card h-100 shadow-sm">
                            <span class="rest-badge">
                                <i class="fa fa-utensils"></i> {{ $res->mon_an_count }} món
                            </span>

                            <div class="card-body d-flex flex-column">
                                @if($res->HinhAnh)
                                    <div class="rest-avatar" style="overflow: hidden; padding: 0; display: block !important;">
                                        <img src="{{ $res->hinh_anh_url }}" alt="{{ $res->TenNhaHang }}" style="width: 100%; height: 100%; object-fit: cover; display: block;">
                                    </div>
                                @else
                                    <div class="rest-avatar" data-letter="{{ mb_substr($res->TenNhaHang, 0, 1, 'UTF-8') }}"></div>
                                @endif

                                <h5 class="card-title mb-2 rest-name" style="font-weight: 600;">{{ $res->TenNhaHang }}</h5>
                                <p class="card-text text-muted mb-1 rest-address">
                                    <i class="fa fa-map-marker"></i>
                                    {{ \Illuminate\Support\Str::limit($res->DiaChi, 80, '...') }}
                                </p>

                                @if($res->SoDienThoai)
                                    <p class="card-text text-muted rest-phone">
                                        <i class="fa fa-phone"></i> {{ $res->SoDienThoai }}
                                    </p>
                                @endif

                                <div class="mt-auto d-flex justify-content-between align-items-center">
                                    <small class="text-muted"></small>
                                    <button type="button" data-id="{{ $res->MaNhaHang }}"
                                        class="btn btn-warning btn-sm rest-cta view-restaurant-menu">
                                        <i class="fa fa-eye"></i> Xem món
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <p class="text-center text-muted">Chưa có nhà hàng nào.</p>
                    </div>
                @endforelse
            </div>

            <!-- Sleek & Premium "Xem thêm" Button Container for Restaurants -->
            <div class="text-center mt-4" id="loadMoreRestaurantsContainer" style="display: block;">
                <button id="loadMoreRestaurantsBtn" class="btn px-5 py-3 rounded-pill font-weight-bold" style="background: linear-gradient(135deg, #ffbe33, #ff9800); color: #222831; border: none; font-size: 14.5px; letter-spacing: 0.5px; box-shadow: 0 8px 25px rgba(255, 190, 51, 0.25); transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94); outline: none;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 12px 30px rgba(255, 190, 51, 0.35)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 8px 25px rgba(255, 190, 51, 0.25)';">
                    <span id="loadMoreRestaurantsText"><i class="fa fa-utensils mr-2"></i> Xem thêm quán ngon</span>
                    <span id="loadMoreRestaurantsSpinner" class="spinner-border spinner-border-sm d-none ml-2" role="status" style="width: 1rem; height: 1rem; color: #222831;"></span>
                </button>
            </div>
        </div>
    </section>



    <!-- client section -->

    <section class="client_section layout_padding-bottom">
        <div class="container">
            <div class="heading_container heading_center psudo_white_primary mb_45">
                <h2>
                    Bình luận
                </h2>
            </div>
            <div class="carousel-wrap row ">
                <div class="owl-carousel client_owl-carousel">
                    @forelse($reviews as $review)
                        <div class="item">
                            <div class="box">
                                <div class="detail-box">
                                    <p>
                                        {{ $review->noi_dung }}
                                    </p>
                                    <h6>
                                        {{ $review->nguoiDung->TenNguoiDung ?? 'Khách hàng' }}
                                    </h6>
                                    <div style="font-size: 12px; margin-top: 8px; color: #6c757d;">
                                        <div style="margin-bottom: 4px;">{!! $review->diem_sao_html !!}</div>
                                        <div style="font-style: italic;">Món: <span style="color: #ffbe33;">{{ $review->monAn->TenMonAn ?? 'Sản phẩm' }}</span></div>
                                    </div>
                                </div>
                                <div class="img-box">
                                    <img src="{{ $review->nguoiDung ? $review->nguoiDung->avatar_url : asset('images/default-avatar.png') }}" 
                                         alt="Avatar" 
                                         class="box-img" 
                                         onerror="this.src='{{ asset('images/client1.jpg') }}'"
                                         style="object-fit: cover; width: 100%; height: 100%;">
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="item">
                            <div class="box">
                                <div class="detail-box">
                                    <p>
                                        Chưa có đánh giá nào từ khách hàng.
                                    </p>
                                    <h6>
                                        Hệ thống TA-FOOD
                                    </h6>
                                </div>
                                <div class="img-box">
                                    <img src="{{ asset('images/favicon.png') }}" alt="" class="box-img" style="object-fit: contain;">
                                </div>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </section>

    <!-- about section -->
    <section class="about_section layout_padding">
        <div class="container  ">

            <div class="row">
                <div class="col-md-6 ">
                    <div class="img-box">
                        <img src="{{ asset('images/about-img.png') }}" alt="">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="detail-box">
                        <div class="heading_container">
                            <h2>
                                We Are TAFOOD
                            </h2>
                        </div>
                        <p>
                            Trang web đặt đồ ăn của chúng tôi được xây dựng nhằm mang đến cho bạn trải nghiệm ẩm
                            thực
                            tiện lợi và nhanh chóng ngay
                            tại nhà. Chỉ với vài thao tác đơn giản trên điện thoại hoặc máy tính, bạn có thể dễ dàng
                            lựa chọn hàng trăm món ăn hấp
                            dẫn từ nhiều nhà hàng uy tín khác nhau, từ cơm, bún, phở, mì cho đến các loại trà sữa,
                            trà đào, cà phê và đồ ăn vặt.
                            Chúng tôi cam kết cung cấp thực đơn đa dạng, giá cả minh bạch, hình ảnh món ăn rõ ràng
                            và thông tin chi tiết để bạn lựa
                            chọn đúng khẩu vị. Đặc biệt, hệ thống hỗ trợ tìm kiếm thông minh giúp bạn phân loại theo
                            món ăn, nhà hàng hoặc mức giá,
                            tiết kiệm thời gian đặt hàng. Mọi đơn hàng sẽ được xử lý nhanh chóng và giao tận nơi
                            đúng giờ, đảm bảo món ăn luôn nóng
                            hổi và tươi ngon. Với phương châm “Ăn ngon – Giao nhanh – Tiện lợi”, chúng tôi hy vọng
                            mang lại sự hài lòng tuyệt đối
                            cho mỗi khách hàng. Hãy để chúng tôi đồng hành cùng bạn trong từng bữa ăn ngon miệng mỗi
                            ngày.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- end about section -->

    <style>
        /* Add to cart button animation */
        .add-to-cart-btn {
            transition: all 0.3s ease;
        }

        .add-to-cart-btn:hover {
            transform: scale(1.1);
            background: #ff6b35 !important;
        }

        .add-to-cart-btn.adding {
            animation: addToCartPulse 0.6s ease;
        }

        @keyframes addToCartPulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.3);
                background: #28a745;
            }

            100% {
                transform: scale(1);
            }
        }
    </style>

    <script>
        // Add CSS animations
        const style = document.createElement('style');
        style.textContent = `
                                                                                                                                                    @keyframes slideInRight {
                                                                                                                                                        from { transform: translateX(100%); opacity: 0; }
                                                                                                                                                        to { transform: translateX(0); opacity: 1; }
                                                                                                                                                    }
                                                                                                                                                    @keyframes slideOutRight {
                                                                                                                                                        from { transform: translateX(0); opacity: 1; }
                                                                                                                                                        to { transform: translateX(100%); opacity: 0; }
                                                                                                                                                    }
                                                                                                                                                `;
        document.head.appendChild(style);
    </script>

    <script>
        // Click-to-copy voucher code
        function copyVoucherCode(code, index) {
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(code);
            } else {
                const el = document.createElement('textarea');
                el.value = code;
                document.body.appendChild(el);
                el.select();
                document.execCommand('copy');
                document.body.removeChild(el);
            }
            const badge = document.getElementById('voucher-code-' + index);
            const feedback = document.getElementById('copy-feedback-' + index);
            if (badge) { badge.style.transform = 'scale(0.95)'; setTimeout(() => badge.style.transform = '', 200); }
            if (feedback) {
                // Clear any running timeouts/animations
                if (feedback.dataset.timeoutId) {
                    clearTimeout(parseInt(feedback.dataset.timeoutId));
                }
                feedback.style.display = 'inline-block';
                feedback.style.opacity = '0';
                feedback.style.transform = 'translateX(-8px)';
                
                // Force a reflow
                feedback.offsetHeight;
                
                feedback.style.transition = 'all 0.35s cubic-bezier(0.25, 0.46, 0.45, 0.94)';
                feedback.style.opacity = '1';
                feedback.style.transform = 'translateX(0)';
                
                const tId = setTimeout(() => {
                    feedback.style.opacity = '0';
                    feedback.style.transform = 'translateX(8px)';
                    setTimeout(() => { feedback.style.display = 'none'; }, 350);
                }, 2000);
                feedback.dataset.timeoutId = tId;
            }
        }

        // Voucher card hover lift
        document.querySelectorAll('.voucher-card').forEach(card => {
            card.addEventListener('mouseenter', () => {
                card.style.transform = 'translateY(-6px)';
                card.style.boxShadow = '0 20px 45px rgba(0,0,0,0.28)';
            });
            card.addEventListener('mouseleave', () => {
                card.style.transform = '';
                card.style.boxShadow = '0 12px 35px rgba(0,0,0,0.2)';
            });
        });
    </script>
    
    <script>
        // Dropdown click outside to close
        document.addEventListener('DOMContentLoaded', function() {
            const dropdown = document.querySelector('.dropdown');
            const dropdownToggle = document.querySelector('.dropdown-toggle');
            const dropdownMenu = document.querySelector('.dropdown-menu');

            if (dropdownToggle && dropdownMenu) {
                // Toggle dropdown on click
                dropdownToggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    dropdownMenu.classList.toggle('show');
                });

                // Close dropdown when clicking outside
                document.addEventListener('click', function(e) {
                    if (dropdown && !dropdown.contains(e.target)) {
                        dropdownMenu.classList.remove('show');
                    }
                });
            }
        });
    </script>

    <!-- Ultra-Premium Restaurant Foods Modal (Bootstrap 4 & 5 Compatible Layout) -->
    <div class="modal fade" id="restaurantFoodsModal" tabindex="-1" role="dialog" aria-labelledby="restaurantFoodsModalLabel" aria-hidden="true" style="backdrop-filter: blur(8px); background-color: rgba(15, 23, 42, 0.5);">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document" style="max-width: 850px;">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 24px; overflow: hidden; background-color: #f8fafc; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25) !important;">
                
                <!-- Header with gorgeous premium dark gradient -->
                <div class="modal-header text-white d-flex flex-column align-items-start border-0 position-relative p-4" style="background: linear-gradient(90deg, rgba(15, 23, 42, 0.65) 0%, rgba(15, 23, 42, 0.1) 100%), url('{{ asset('images/bg2.jpg') }}') no-repeat center center; background-size: cover; min-height: 140px; border-bottom: 3px solid #ffbe33;">
                    <button type="button" class="close text-white position-absolute modal-custom-close" data-dismiss="modal" aria-label="Close" style="top: 20px; right: 20px; z-index: 10; font-size: 28px; opacity: 0.8; outline: none; border: none; background: none; transition: all 0.2s ease;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    
                    <div class="d-flex align-items-center mb-2 mt-2">
                        <!-- Glowing avatar container -->
                        <div id="modalRestAvatar" class="d-flex align-items-center justify-content-center text-white font-weight-bold rounded-circle mr-3 shadow" style="width: 60px; height: 60px; min-width: 60px; background: rgba(255,255,255,0.08); border: 2px solid rgba(255, 190, 51, 0.6); font-size: 26px; font-weight: bold; text-shadow: 0 2px 4px rgba(0,0,0,0.3);">R</div>
                        <div>
                            <h4 class="modal-title font-weight-bold mb-0 text-white" id="modalRestName" style="letter-spacing: -0.2px; font-size: 1.5rem; text-shadow: 0 2px 4px rgba(0,0,0,0.4);">Tên nhà hàng</h4>
                            <span class="badge badge-warning mt-1 shadow-sm" id="modalRestFoodsCount" style="font-size: 11.5px; font-weight: 700; background-color: #ffbe33; color: #1e293b; padding: 4px 10px; border-radius: 20px;"><i class="fa fa-utensils mr-1"></i> 0 món ăn</span>
                        </div>
                    </div>
                    
                    <div class="w-100 mt-3 pt-2 text-white-50 border-top" style="font-size: 13.5px; border-top-color: rgba(255,255,255,0.08) !important;">
                        <div class="mb-1"><i class="fa fa-map-marker-alt mr-2 text-warning"></i> <span id="modalRestAddress">Địa chỉ nhà hàng</span></div>
                        <div id="modalRestPhoneContainer" class="d-inline-block"><i class="fa fa-phone mr-2 text-warning"></i> <span id="modalRestPhone">Số điện thoại</span></div>
                    </div>
                </div>
                
                <!-- Body with responsive glassmorphic food list -->
                <div class="modal-body bg-light food_section px-4 py-4" style="max-height: 55vh; overflow-y: auto;">
                    
                    <!-- Beautiful Menu Tabs Navigation -->
                    <div id="modalMenuTabs" class="d-none mb-4">
                        <div class="d-flex justify-content-center">
                            <div class="nav-pills-wrapper p-1 bg-white shadow-sm d-inline-flex" style="border-radius: 30px; border: 1px solid #e2e8f0;">
                                <button type="button" class="btn px-4 py-2 font-weight-bold active-menu-tab" id="tabMainBtn" style="border-radius: 25px; font-size: 13px; transition: all 0.3s ease; border: none; outline: none;">
                                    🍽️ Thực đơn chính
                                </button>
                                <button type="button" class="btn px-4 py-2 font-weight-bold text-muted" id="tabBestBtn" style="border-radius: 25px; font-size: 13px; transition: all 0.3s ease; border: none; outline: none; background: transparent;">
                                    🔥 Món bán chạy nhất
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Main Menu Content Section -->
                    <div id="modalMainSection">
                        <h5 class="font-weight-bold mb-3 text-secondary position-relative pb-2" style="font-size: 15px; letter-spacing: 0.5px; text-transform: uppercase;">
                            Thực đơn của quán
                            <div class="position-absolute bg-warning" style="width: 40px; height: 3px; bottom: 0; left: 0; border-radius: 2px;"></div>
                        </h5>
                        
                        <!-- Beautiful loading spinner -->
                        <div id="modalFoodsLoading" class="text-center py-5">
                            <div class="spinner-border text-warning" role="status" style="width: 3rem; height: 3rem;">
                                <span class="sr-only">Đang tải...</span>
                            </div>
                            <p class="text-muted mt-3 font-italic" style="font-size: 14px;">Đang chuẩn bị thực đơn thơm ngon...</p>
                        </div>
                        
                        <!-- Dynamic Empty state -->
                        <div id="modalFoodsEmpty" class="text-center py-5 d-none">
                            <i class="fa fa-utensils fa-4x text-muted opacity-40 mb-3"></i>
                            <p class="text-muted font-weight-bold" style="font-size: 15px;">Quán này hiện chưa đăng bán món ăn nào.</p>
                        </div>

                        <!-- Food Cards Grid -->
                        <div class="row grid" id="modalFoodsGrid">
                            <!-- Dynamically filled with beautiful food cards -->
                        </div>
                    </div>

                    <!-- Best Sellers Content Section (Hidden by default) -->
                    <div id="modalBestSellersSection" class="d-none">
                        <h5 class="font-weight-bold mb-3 position-relative pb-2" style="font-size: 15px; letter-spacing: 0.5px; text-transform: uppercase; color: #ff8f00 !important; font-weight: 800;">
                            🔥 Món bán chạy nhất
                            <div class="position-absolute" style="width: 40px; height: 3px; bottom: 0; left: 0; border-radius: 2px; background: linear-gradient(90deg, #ffbe33, #ff8f00);"></div>
                        </h5>
                        <div class="row grid" id="modalBestSellersGrid">
                            <!-- Filled dynamically with premium Best Seller cards -->
                        </div>
                    </div>
                </div>
                
                <!-- Footer with clean layout -->
                <div class="modal-footer border-top bg-white p-3 d-flex justify-content-center align-items-center" style="border-top-color: #f1f5f9 !important;">
                    <span class="text-muted font-italic" style="font-size: 12px;"><i class="fa fa-info-circle mr-1 text-warning"></i> Bấm "Thêm giỏ" để đặt món nhanh chóng</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Custom CSS for dynamic modal transitions and premium card hover states -->
    <style>
        .active-menu-tab {
            background-color: #ffbe33 !important;
            color: #1e293b !important;
            box-shadow: 0 4px 10px rgba(255, 190, 51, 0.3) !important;
        }

        .modal-custom-close:hover {
            transform: rotate(90deg) scale(1.1);
            color: #ffbe33 !important;
            opacity: 1 !important;
        }

        .modal-food-item-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid rgba(0, 0, 0, 0.05) !important;
        }

        .modal-food-item-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05) !important;
            border-color: rgba(255, 190, 51, 0.3) !important;
        }

        .modal-food-image-box img {
            transition: transform 0.5s ease;
        }

        .modal-food-item-card:hover .modal-food-image-box img {
            transform: scale(1.08);
        }

        .modal-add-cart-btn {
            box-shadow: 0 2px 4px rgba(255, 190, 51, 0.2);
            transition: all 0.2s ease !important;
        }

        .modal-add-cart-btn:hover {
            transform: scale(1.05);
            background-color: #e0a800 !important;
            box-shadow: 0 6px 12px rgba(255, 190, 51, 0.3) !important;
        }

        .modal-add-cart-btn.adding {
            animation: modalAddToCartPulse 0.5s ease;
        }

        @keyframes modalAddToCartPulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.25); background-color: #28a745 !important; color: white !important; }
            100% { transform: scale(1); }
        }

        /* Premium Compact Modal Food Card styling */
        .modal-food-card {
            background: linear-gradient(to bottom, #f1f2f3 15px, #222831 15px) !important;
            border-radius: 16px !important;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08) !important;
            border: 1px solid rgba(0, 0, 0, 0.03) !important;
            transition: all 0.3s ease !important;
            margin-top: 15px !important;
        }

        .modal-food-card:hover {
            transform: translateY(-4px) !important;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15) !important;
            border-color: rgba(255, 190, 51, 0.3) !important;
        }

        .modal-food-card .img-box {
            height: 130px !important; /* Extremely neat and compact image height */
            background: #f1f2f3 !important;
            border-radius: 0 0 0 24px !important;
            overflow: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .modal-food-card .img-box img {
            width: 100% !important;
            height: 100% !important;
            object-fit: cover !important;
        }

        .modal-food-card .detail-box {
            padding: 12px 14px !important; /* Compact spacing */
        }

        .modal-food-card .detail-box h5 {
            font-size: 13.5px !important;
            font-weight: 700 !important;
            margin-bottom: 4px !important;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            line-height: 1.3 !important;
        }

        .modal-food-card .detail-box h5 a {
            color: #ffffff !important;
            font-size: 13.5px !important;
            font-weight: 700 !important;
        }

        .modal-food-card .detail-box p {
            font-size: 11px !important;
            color: rgba(255, 255, 255, 0.6) !important;
            margin-bottom: 6px !important;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .modal-food-card .rating {
            font-size: 10px !important;
            margin-bottom: 6px !important;
        }

        .modal-food-card .rating i {
            font-size: 10px !important;
        }

        .modal-food-card .options {
            display: flex !important;
            justify-content: space-between !important;
            align-items: center !important;
            margin-top: 10px !important;
            width: 100% !important;
        }

        .modal-food-card .options h6 {
            font-size: 14px !important;
            font-weight: 700 !important;
            color: #ffbe33 !important;
            margin: 0 !important;
            line-height: 1.2 !important;
            align-self: center !important;
        }

        /* Ensure both eye and cart buttons inside the modal are perfectly equal, circular, and centered */
        .modal-food-card .options .btn,
        .modal-food-card .options .btn-warning,
        .modal-food-card .options .modal-add-cart-btn {
            width: 34px !important;
            height: 34px !important;
            padding: 0 !important;
            margin: 0 !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            border-radius: 50% !important;
            background-color: #ffbe33 !important;
            color: #222831 !important;
            border: none !important;
            box-shadow: 0 2px 6px rgba(255, 190, 51, 0.2) !important;
            transition: all 0.2s ease !important;
            line-height: 1 !important;
            vertical-align: middle !important;
            text-align: center !important;
        }

        .modal-food-card .options .btn:hover,
        .modal-food-card .options .btn-warning:hover,
        .modal-food-card .options .modal-add-cart-btn:hover {
            transform: scale(1.1) !important;
            background-color: #e0a800 !important;
            color: #222831 !important;
            box-shadow: 0 4px 12px rgba(255, 190, 51, 0.4) !important;
        }

        /* Center the FontAwesome icon inside the circular button perfectly */
        .modal-food-card .options .btn i,
        .modal-food-card .options .btn-warning i,
        .modal-food-card .options .modal-add-cart-btn i {
            font-size: 14px !important;
            line-height: 1 !important;
            margin: 0 !important;
            padding: 0 !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            width: auto !important;
            height: auto !important;
            color: #222831 !important;
        }

        .modal-food-card .options .d-flex {
            gap: 4px !important;
            align-items: center !important;
        }
    </style>

    <!-- Ajax API dynamic Restaurant Menu Fetch & Render Script -->
    <script>
        window.addEventListener('load', function() {
            var $ = window.jQuery;
            if (!$) { console.error('jQuery not loaded'); return; }
            var IS_AUTHENTICATED = {{ Auth::check() ? 'true' : 'false' }};
            // Tab switching logic for restaurant modal
            const tabMainBtn = document.getElementById('tabMainBtn');
            const tabBestBtn = document.getElementById('tabBestBtn');
            const modalMainSection = document.getElementById('modalMainSection');
            const modalBestSellersSection = document.getElementById('modalBestSellersSection');

            if (tabMainBtn && tabBestBtn) {
                tabMainBtn.addEventListener('click', function() {
                    tabMainBtn.classList.add('active-menu-tab');
                    tabMainBtn.classList.remove('text-muted');
                    tabMainBtn.style.background = '';
                    
                    tabBestBtn.classList.remove('active-menu-tab');
                    tabBestBtn.classList.add('text-muted');
                    tabBestBtn.style.background = 'transparent';
                    
                    modalMainSection.classList.remove('d-none');
                    modalBestSellersSection.classList.add('d-none');
                });

                tabBestBtn.addEventListener('click', function() {
                    tabBestBtn.classList.add('active-menu-tab');
                    tabBestBtn.classList.remove('text-muted');
                    tabBestBtn.style.background = '';
                    
                    tabMainBtn.classList.remove('active-menu-tab');
                    tabMainBtn.classList.add('text-muted');
                    tabMainBtn.style.background = 'transparent';
                    
                    modalBestSellersSection.classList.remove('d-none');
                    modalMainSection.classList.add('d-none');
                });
            }

            // Event delegation for "Xem món" button (works even for dynamically loaded buttons)
            $(document).on('click', '.view-restaurant-menu', async function(e) {
                e.preventDefault();
                const id = $(this).data('id');
                if (!id) return;
                
                // Show modal and loading spinner
                $('#restaurantFoodsModal').modal('show');
                document.getElementById('modalFoodsLoading').classList.remove('d-none');
                document.getElementById('modalFoodsEmpty').classList.add('d-none');
                document.getElementById('modalFoodsGrid').innerHTML = '';
                
                // Set loading placeholders
                document.getElementById('modalRestName').textContent = 'Đang tải thực đơn...';
                document.getElementById('modalRestAddress').textContent = 'Đang tìm địa chỉ...';
                document.getElementById('modalRestPhone').textContent = '';
                document.getElementById('modalRestFoodsCount').innerHTML = '<i class="fa fa-utensils mr-1"></i> 0 món';
                
                try {
                    const response = await fetch(`/api/v1/restaurants/${id}`);
                        if (!response.ok) throw new Error('Fetch error');
                        const result = await response.json();
                        
                        if (result.success) {
                            const rest = result.data.restaurant;
                            const foods = result.data.foods;
                            
                            // Bind Restaurant Info
                            document.getElementById('modalRestName').textContent = rest.TenNhaHang;
                            document.getElementById('modalRestAddress').textContent = rest.DiaChi;
                            
                            if (rest.SoDienThoai) {
                                document.getElementById('modalRestPhoneContainer').classList.remove('d-none');
                                document.getElementById('modalRestPhone').textContent = rest.SoDienThoai;
                            } else {
                                document.getElementById('modalRestPhoneContainer').classList.add('d-none');
                            }
                            
                            if (rest.hinh_anh_url) {
                                document.getElementById('modalRestAvatar').innerHTML = `<img src="${rest.hinh_anh_url}" alt="${rest.TenNhaHang}" style="width: 100%; height: 100%; object-fit: cover; display: block;">`;
                                document.getElementById('modalRestAvatar').style.padding = '0';
                                document.getElementById('modalRestAvatar').style.overflow = 'hidden';
                                document.getElementById('modalRestAvatar').style.display = 'block';
                            } else {
                                const firstLetter = rest.TenNhaHang ? rest.TenNhaHang.substring(0, 1).toUpperCase() : 'R';
                                document.getElementById('modalRestAvatar').textContent = firstLetter;
                                document.getElementById('modalRestAvatar').style.padding = '';
                                document.getElementById('modalRestAvatar').style.overflow = '';
                                document.getElementById('modalRestAvatar').style.display = '';
                            }
                            
                            document.getElementById('modalRestFoodsCount').innerHTML = `<i class="fa fa-utensils mr-1"></i> ${foods.length} món ăn`;
                            
                            // Hide spinner
                            document.getElementById('modalFoodsLoading').classList.add('d-none');
                            
                            if (foods.length === 0) {
                                document.getElementById('modalFoodsEmpty').classList.remove('d-none');
                                document.getElementById('modalBestSellersSection').classList.add('d-none');
                            } else {
                                document.getElementById('modalFoodsEmpty').classList.add('d-none');
                                
                                const sortedFoods = [...foods].sort((a, b) => {
                                    if (b.diem_trung_binh !== a.diem_trung_binh) {
                                        return b.diem_trung_binh - a.diem_trung_binh;
                                    }
                                    return (b.tong_binh_luan || 0) - (a.tong_binh_luan || 0);
                                });
                                
                                const bestSellers = sortedFoods.slice(0, 2);
                                
                                if (foods.length >= 3 && bestSellers.length > 0) {
                                    document.getElementById('modalMenuTabs').classList.remove('d-none');
                                    if (tabMainBtn) { tabMainBtn.click(); }
                                    
                                    const bestGrid = document.getElementById('modalBestSellersGrid');
                                    bestGrid.innerHTML = bestSellers.map(food => {
                                         const imageSrc = food.hinh_anh_url;
                                        const formattedPrice = new Intl.NumberFormat('vi-VN').format(food.Gia);
                                        let starsHtml = '';
                                        const avgRating = food.diem_trung_binh || 0;
                                        const floorRating = Math.floor(avgRating);
                                        const ceilRating = Math.ceil(avgRating);
                                        for (let i = 1; i <= 5; i++) {
                                            if (i <= floorRating) {
                                                starsHtml += '<i class="fa fa-star text-warning mr-1"></i>';
                                            } else if (i === ceilRating && (avgRating - floorRating) >= 0.5) {
                                                starsHtml += '<i class="fa fa-star-half-alt text-warning mr-1"></i>';
                                            } else {
                                                starsHtml += '<i class="fa fa-star text-muted mr-1"></i>';
                                            }
                                        }
                                        let badgeHtml = avgRating >= 4.5 && (food.tong_binh_luan || 0) >= 1
                                            ? `<span class="badge position-absolute shadow-sm" style="top:10px;left:10px;z-index:10;background:linear-gradient(135deg,#ffbe33,#ff9800);color:#222831;font-weight:800;padding:4px 8px;border-radius:20px;font-size:9px;border:none;"><i class="fa fa-star"></i> ${parseFloat(avgRating).toFixed(1)} Yêu thích</span>`
                                            : `<span class="badge position-absolute" style="top:10px;left:10px;z-index:10;background:linear-gradient(135deg,#ffbe33 0%,#ff9800 100%);color:#1e293b;font-weight:800;padding:4px 8px;border-radius:20px;font-size:9px;border:none;">${food.DanhMuc}</span>`;

                                        return `
                                            <div class="col-6 col-sm-6 col-lg-4 mb-3">
                                                <div class="box h-100 modal-food-card" data-detail-url="/food/${food.MaMonAn}?from_restaurant=${id}" style="cursor:pointer;">
                                                    <div>
                                                        <div class="img-box position-relative">
                                                            <img src="${imageSrc}" alt="${food.TenMonAn}" style="transition:transform 0.5s ease;">
                                                            ${badgeHtml}
                                                        </div>
                                                        <div class="detail-box">
                                                            <h5><a href="/food/${food.MaMonAn}?from_restaurant=${id}" class="text-decoration-none text-light">${food.TenMonAn}</a></h5>
                                                            ${food.MoTa ? `<p style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${food.MoTa}</p>` : ''}
                                                            <div class="rating mb-2">${starsHtml}<small class="text-muted ms-1">(${food.tong_binh_luan || 0})</small></div>
                                                            <div class="options">
                                                                <h6>${formattedPrice} đ</h6>
                                                                <div class="d-flex gap-3 align-items-center">
                                                                    <a href="/food/${food.MaMonAn}?from_restaurant=${id}" class="btn btn-warning rounded-circle d-flex align-items-center justify-content-center shadow-sm" title="Xem chi tiết"><i class="fa fa-eye"></i></a>
                                                                    <button type="button" class="btn btn-warning rounded-circle d-flex align-items-center justify-content-center shadow-sm add-to-cart-btn modal-add-cart-btn" data-id="${food.MaMonAn}" data-name="${food.TenMonAn}" data-price="${food.Gia}" data-image="${imageSrc}" title="Thêm vào giỏ"><i class="fa fa-shopping-cart"></i></button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        `;
                                    }).join('');
                                } else {
                                    document.getElementById('modalMenuTabs').classList.add('d-none');
                                    document.getElementById('modalBestSellersSection').classList.add('d-none');
                                    document.getElementById('modalMainSection').classList.remove('d-none');
                                }

                                const grid = document.getElementById('modalFoodsGrid');
                                const sortedFoodsForGrid = [...foods].sort((a, b) => {
                                    const aIsBest = foods.length >= 3 && bestSellers.some(b_item => b_item.MaMonAn === a.MaMonAn);
                                    const bIsBest = foods.length >= 3 && bestSellers.some(b_item => b_item.MaMonAn === b.MaMonAn);
                                    if (aIsBest && !bIsBest) return -1;
                                    if (!aIsBest && bIsBest) return 1;
                                    return 0;
                                });
                                grid.innerHTML = sortedFoodsForGrid.map(food => {
                                    const imageSrc = food.hinh_anh_url;
                                    const formattedPrice = new Intl.NumberFormat('vi-VN').format(food.Gia);
                                    const isBestSeller = foods.length >= 3 && bestSellers.some(b => b.MaMonAn === food.MaMonAn);
                                    let starsHtml = '';
                                    const avgRating = food.diem_trung_binh || 0;
                                    const floorRating = Math.floor(avgRating);
                                    const ceilRating = Math.ceil(avgRating);
                                    for (let i = 1; i <= 5; i++) {
                                        if (i <= floorRating) {
                                            starsHtml += '<i class="fa fa-star text-warning mr-1"></i>';
                                        } else if (i === ceilRating && (avgRating - floorRating) >= 0.5) {
                                            starsHtml += '<i class="fa fa-star-half-alt text-warning mr-1"></i>';
                                        } else {
                                            starsHtml += '<i class="fa fa-star text-muted mr-1"></i>';
                                        }
                                    }
                                    const isFav = food.is_favorite;
                                    const heartHtml = IS_AUTHENTICATED ? `
                                        <button type="button" class="heart-fav-btn toggle-fav-btn" data-id="${food.MaMonAn}" title="${isFav ? 'Xóa khỏi yêu thích' : 'Thêm vào yêu thích'}" style="position:absolute;top:10px;right:10px;width:32px;height:32px;background:rgba(255,255,255,0.9);border:none;border-radius:50%;display:flex;align-items:center;justify-content:center;cursor:pointer;z-index:10;box-shadow:0 4px 8px rgba(0,0,0,0.15);transition:all 0.2s ease;">
                                            <i class="${isFav ? 'fa fa-heart text-danger' : 'far fa-heart text-muted'}" style="font-size:14px;transition:transform 0.2s ease;"></i>
                                        </button>
                                    ` : `
                                        <button type="button" class="heart-fav-btn require-login-fav-btn" title="Đăng nhập để lưu yêu thích" style="position:absolute;top:10px;right:10px;width:32px;height:32px;background:rgba(255,255,255,0.9);border:none;border-radius:50%;display:flex;align-items:center;justify-content:center;cursor:pointer;z-index:10;box-shadow:0 4px 8px rgba(0,0,0,0.15);transition:all 0.2s ease;">
                                            <i class="far fa-heart text-muted" style="font-size:14px;"></i>
                                        </button>
                                    `;
                                    let badgeHtml = isBestSeller
                                        ? `<span class="badge position-absolute shadow-sm" style="top:10px;left:10px;z-index:10;background:linear-gradient(135deg,#ff8f00 0%,#ff5252 100%);color:#fff;font-weight:800;padding:4px 8px;border-radius:20px;font-size:9px;border:none;">🔥 Bán chạy</span>`
                                        : `<span class="badge position-absolute" style="top:10px;left:10px;z-index:10;background:linear-gradient(135deg,#ffbe33 0%,#ff9800 100%);color:#1e293b;font-weight:800;padding:4px 8px;border-radius:20px;font-size:9px;border:none;">${food.DanhMuc}</span>`;

                                    return `
                                        <div class="col-6 col-sm-6 col-lg-4 mb-3">
                                            <div class="box h-100 modal-food-card" data-detail-url="/food/${food.MaMonAn}?from_restaurant=${id}" style="cursor:pointer;">
                                                <div>
                                                    <div class="img-box position-relative">
                                                        <img src="${imageSrc}" alt="${food.TenMonAn}" style="transition:transform 0.5s ease;">
                                                        ${badgeHtml}
                                                        ${heartHtml}
                                                    </div>
                                                    <div class="detail-box d-flex flex-column" style="height:calc(100% - 130px);">
                                                        <h5><a href="/food/${food.MaMonAn}?from_restaurant=${id}" class="text-decoration-none text-light">${food.TenMonAn}</a></h5>
                                                        ${food.MoTa ? `<p style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${food.MoTa}</p>` : ''}
                                                        <div class="rating mb-2">${starsHtml}<small class="text-muted ms-1">(${food.tong_binh_luan || 0})</small></div>
                                                        <div class="options mt-auto d-flex justify-content-between align-items-center">
                                                            <h6 class="mb-0">${formattedPrice} đ</h6>
                                                            <div class="d-flex align-items-center" style="gap:6px;">
                                                                <a href="/food/${food.MaMonAn}?from_restaurant=${id}" class="btn btn-warning rounded-circle shadow-sm" style="width:34px;height:34px;min-width:34px;min-height:34px;max-width:34px;max-height:34px;padding:0;display:flex!important;align-items:center!important;justify-content:center!important;flex-shrink:0;" title="Xem chi tiết"><i class="fa fa-eye" style="font-size:13px;margin:0;line-height:1;"></i></a>
                                                                <button type="button" class="btn btn-warning rounded-circle shadow-sm add-to-cart-btn modal-add-cart-btn" style="width:34px;height:34px;min-width:34px;min-height:34px;max-width:34px;max-height:34px;padding:0;display:flex!important;align-items:center!important;justify-content:center!important;flex-shrink:0;" data-id="${food.MaMonAn}" data-name="${food.TenMonAn}" data-price="${food.Gia}" data-image="${imageSrc}" title="Thêm vào giỏ"><i class="fa fa-shopping-cart" style="font-size:13px;margin:0;line-height:1;"></i></button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    `;
                                }).join('');
                            }
                        }
                } catch (error) {
                    console.error('Failed to load menu:', error);
                    document.getElementById('modalFoodsLoading').classList.add('d-none');
                    document.getElementById('modalRestName').textContent = 'Lỗi kết nối';
                    document.getElementById('modalFoodsEmpty').classList.remove('d-none');
                    document.getElementById('modalFoodsEmpty').innerHTML = `
                        <i class="fa fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                        <p class="text-danger font-weight-bold">Không thể kết nối API. Vui lòng tải lại trang!</p>
                    `;
                }
            });
            // Auto-open restaurant modal if open_restaurant query parameter is present (User UX back flow)
            (function() {
                const urlParams = new URLSearchParams(window.location.search);
                const openRestId = urlParams.get('open_restaurant');
                if (openRestId) {
                    // Find the "Xem món" button with matching data-id and trigger a click on it
                    const targetButton = document.querySelector(`.view-restaurant-menu[data-id="${openRestId}"]`);
                    if (targetButton) {
                        // Trigger click event after a short delay to ensure assets are fully loaded and styled
                        setTimeout(() => {
                            targetButton.click();
                            
                            // Clean up URL parameter cleanly
                            const cleanUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;
                            window.history.replaceState({ path: cleanUrl }, '', cleanUrl);
                        }, 300);
                    }
                }
            })();

            // JS Handler for Dynamic Voucher click (using event delegation)
            document.addEventListener('click', async function(e) {
                const button = e.target.closest('.pre-apply-btn');
                if (!button) return;

                e.preventDefault();
                const code = button.getAttribute('data-code');

                // Check if user is logged in
                const userIdMeta = document.querySelector('meta[name="user-id"]');
                if (!userIdMeta) {
                    if (typeof showAlert === 'function') {
                        showAlert('guest_checkout', 'Vui lòng Đăng nhập hoặc Đăng ký nhanh tài khoản để có thể lưu và sử dụng mã khuyến mãi.');
                    } else {
                        alert('Vui lòng Đăng nhập hoặc Đăng ký nhanh tài khoản để có thể lưu và sử dụng mã khuyến mãi.');
                    }
                    return;
                }

                // Logged in -> Call API
                try {
                    const response = await fetch("{{ route('checkout.pre-apply-voucher') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ voucher_code: code })
                    });

                    const result = await response.json();
                    
                    if (response.ok && result.success) {
                        // Copy to clipboard silently
                        navigator.clipboard.writeText(code).catch(() => {});
                        
                        // Show premium glassmorphic success notification
                        if (typeof showAlert === 'function') {
                            showAlert('voucher_saved', code);
                        } else {
                            alert('Đã lưu mã ' + code + ' thành công!');
                        }
                        
                        // Update button styling dynamically for all buttons with this code (both in main list and modal)
                        document.querySelectorAll(`.pre-apply-btn[data-code="${code}"]`).forEach(btn => {
                            btn.innerHTML = 'Đã lưu <i class="fa fa-check"></i>';
                            btn.style.background = 'linear-gradient(135deg, #22c55e, #16a34a)';
                            btn.style.color = '#ffffff';
                            btn.style.boxShadow = '0 3px 8px rgba(34, 197, 94, 0.3)';
                        });
                    } else {
                        if (typeof showAlert === 'function') {
                            showAlert('danger', result.message || 'Không thể lưu mã lúc này');
                        } else {
                            alert(result.message || 'Không thể lưu mã lúc này');
                        }
                    }
                } catch (error) {
                    console.error('Pre-apply API error:', error);
                    if (typeof showAlert === 'function') {
                        showAlert('danger', 'Lỗi kết nối hệ thống. Vui lòng thử lại!');
                    } else {
                        alert('Lỗi kết nối hệ thống. Vui lòng thử lại!');
                    }
                }
            });
        });
    </script>

    <!-- Modal Xem tất cả Voucher -->
    <div class="modal fade" id="allVouchersModal" tabindex="-1" role="dialog" aria-labelledby="allVouchersModalLabel" aria-hidden="true">
        <style>
            #allVouchersModal .modal-body::-webkit-scrollbar {
                width: 6px;
            }
            #allVouchersModal .modal-body::-webkit-scrollbar-track {
                background: rgba(0, 0, 0, 0.03);
                border-radius: 10px;
            }
            #allVouchersModal .modal-body::-webkit-scrollbar-thumb {
                background: rgba(255, 190, 51, 0.4);
                border-radius: 10px;
            }
            #allVouchersModal .modal-body::-webkit-scrollbar-thumb:hover {
                background: rgba(255, 190, 51, 0.6);
            }
        </style>
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content" style="background: #222831 !important; border: none !important; border-radius: 24px; color: #1e293b; box-shadow: 0 20px 40px rgba(0,0,0,0.15); overflow: hidden;">
                <div class="modal-header" style="background: #222831; border-bottom: 1px solid rgba(255, 255, 255, 0.1); padding: 20px 24px;">
                    <h5 class="modal-title font-weight-bold text-warning d-flex align-items-center" id="allVouchersModalLabel" style="font-size: 20px; color: #ffbe33 !important;">
                        <i class="fa fa-ticket-alt mr-2" style="font-size: 22px;"></i> Kho Khuyến Mãi Độc Quyền
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity: 0.8; font-size: 28px; line-height: 1; outline: none; background: transparent; border: none; color: white !important;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="padding: 24px; max-height: 70vh; overflow-y: auto; scrollbar-width: thin; background: #f8f9fa;">
                    <div class="row">
                        @forelse($allPromotions as $index => $promo)
                            @php
                                $daysLeft = (int) ceil(now()->diffInDays($promo->NgayKetThuc, false));
                                $urgencyColor = $daysLeft <= 2 ? '#ef4444' : ($daysLeft <= 5 ? '#f59e0b' : '#22c55e');
                                $urgencyIcon  = $daysLeft <= 2 ? '🔥' : ($daysLeft <= 5 ? '⚡' : '🗓️');
                                $usedPct = ($promo->SoLuongToiDa > 0)
                                      ? min(100, round(($promo->SoLuongDaSuDung / $promo->SoLuongToiDa) * 100))
                                      : null;
                            @endphp
                            <div class="col-md-6 mb-4 d-flex align-items-stretch">
                                <div class="premium-ticket-card" style="display: flex; width: 100%; min-height: 140px; background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 8px 20px rgba(0,0,0,0.06); border: 1px solid #e2e8f0; position: relative; transition: transform 0.3s, box-shadow 0.3s;"
                                     onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 12px 25px rgba(255, 190, 51, 0.15)';"
                                     onmouseout="this.style.transform='none'; this.style.boxShadow='0 8px 20px rgba(0,0,0,0.06)';"
                                     data-card-id="{{ $promo->MaCode }}">
                                    
                                    <!-- Left Side: Vibrant Gold Gradient Theme -->
                                    <div class="ticket-left" style="width: 105px; background: linear-gradient(135deg, #ffbe33, #ff9800); display: flex; flex-direction: column; align-items: center; justify-content: center; color: #222831; padding: 12px; text-align: center; position: relative; flex-shrink: 0; border-right: 2px dashed rgba(34, 40, 49, 0.15);">
                                        <i class="fa fa-ticket-alt mb-2" style="font-size: 24px; color: #222831; opacity: 0.85;"></i>
                                        <span style="font-size: 22px; font-weight: 850; line-height: 1; color: #222831; font-family: 'Poppins', sans-serif;">{{ $promo->PhanTramFormat }}</span>
                                        <span style="font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; margin-top: 3px; color: #222831; opacity: 0.85;">Giảm</span>
                                        
                                        <!-- Circular notch top (cutout matching modal-body background) -->
                                        <div style="position: absolute; width: 16px; height: 16px; background: #f8f9fa; border-radius: 50%; top: -8px; right: -9px; z-index: 10;"></div>
                                        <!-- Circular notch bottom (cutout matching modal-body background) -->
                                        <div style="position: absolute; width: 16px; height: 16px; background: #f8f9fa; border-radius: 50%; bottom: -8px; right: -9px; z-index: 10;"></div>
                                    </div>

                                    <!-- Right Side: Content & Actions -->
                                    <div class="ticket-right" style="flex: 1; padding: 14px 16px; display: flex; flex-direction: column; justify-content: space-between; position: relative; min-width: 0; background: #ffffff;">
                                        <div>
                                            <!-- Expiry Days Badge & Min Order -->
                                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px; flex-wrap: wrap; gap: 4px;">
                                                <span style="color: {{ $urgencyColor }}; font-weight: 700; font-size: 11px;">
                                                    {{ $urgencyIcon }}
                                                    @if($daysLeft <= 0)
                                                        Hết hạn hôm nay!
                                                    @elseif($daysLeft == 1)
                                                        Còn 1 ngày
                                                    @else
                                                        Còn {{ $daysLeft }} ngày
                                                    @endif
                                                </span>
                                                @if($promo->DonHangToiThieu > 0)
                                                    <span style="background: rgba(255, 190, 51, 0.1); padding: 2px 6px; border-radius: 4px; color: #b45309; font-size: 10px; font-weight: 700;">
                                                        Đơn ≥ {{ number_format($promo->DonHangToiThieu, 0, ',', '.') }}đ
                                                    </span>
                                                @endif
                                            </div>

                                            <!-- Description -->
                                            <h6 style="color: #222831; font-weight: 700; font-size: 13.5px; margin: 0 0 4px 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $promo->MoTa }}">
                                                {{ $promo->MoTa ?? 'Khuyến mãi đặc biệt' }}
                                            </h6>
                                            
                                            @if($promo->GiamToiDa > 0)
                                                <div style="font-size: 10.5px; color: #4b5563; margin-bottom: 4px;">
                                                    Giảm tối đa: <span style="color: #ff9800; font-weight: 700;">{{ number_format($promo->GiamToiDa, 0, ',', '.') }}đ</span>
                                                </div>
                                            @else
                                                <div style="font-size: 10.5px; color: #4b5563; margin-bottom: 4px;">
                                                    Giảm tối đa: <span style="color: #22c55e; font-weight: 700;">Không giới hạn</span>
                                                </div>
                                            @endif
                                        </div>

                                        <!-- Bottom Row: Code & Save Button -->
                                        <div style="display: flex; align-items: center; justify-content: space-between; gap: 8px; margin-top: 4px; border-top: 1px dashed #e2e8f0; padding-top: 8px;">
                                            <div style="background: rgba(34, 40, 49, 0.05); border: 1px dashed rgba(34, 40, 49, 0.2); color: #222831; font-family: monospace; font-weight: 700; font-size: 11px; padding: 4px 8px; border-radius: 6px; letter-spacing: 0.5px;">
                                                Mã: {{ $promo->MaCode }}
                                            </div>
                                            
                                            <a href="javascript:void(0)" class="pre-apply-btn" data-code="{{ $promo->MaCode }}"
                                               style="border-radius: 20px; padding: 5px 14px; background: linear-gradient(135deg, #ffbe33, #ff9800); color: #222831; font-weight: 700; font-size: 11px; text-decoration: none; transition: all 0.2s; border: none; box-shadow: 0 3px 8px rgba(255, 190, 51, 0.25);">
                                                Lưu mã
                                            </a>
                                        </div>

                                        <!-- Progress Bar / Usage count -->
                                        <div style="margin-top: 6px;">
                                            <div style="display: flex; justify-content: space-between; font-size: 9px; color: #6b7280; margin-bottom: 2px;">
                                                @if($usedPct !== null)
                                                    <span>Đã dùng {{ $promo->SoLuongDaSuDung }}/{{ $promo->SoLuongToiDa }} lượt</span>
                                                    <span style="color: {{ $usedPct >= 80 ? '#ef4444' : '#6b7280' }}; font-weight: {{ $usedPct >= 80 ? '700' : '400' }};">
                                                        {{ $usedPct >= 80 ? '🔥 Sắp hết!' : 'Còn ' . ($promo->SoLuongToiDa - $promo->SoLuongDaSuDung) . ' lượt' }}
                                                    </span>
                                                @else
                                                    <span>Lượt dùng: Không giới hạn</span>
                                                    <span style="color: #22c55e; font-weight: 700;">🟢 Khả dụng</span>
                                                @endif
                                            </div>
                                            <div style="background: #e5e7eb; border-radius: 99px; height: 3.5px; overflow: hidden;">
                                                <div style="height: 100%; width: {{ $usedPct ?? 100 }}%; border-radius: 99px; background: {{ ($usedPct ?? 0) >= 80 ? 'linear-gradient(90deg, #ef4444, #f97316)' : 'linear-gradient(90deg, #ffbe33, #22c55e)' }}; transition: width .5s ease;"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5 w-100">
                                <div class="mb-3" style="font-size: 50px; opacity: 0.3;">🎫</div>
                                <p class="text-muted" style="font-size: 15px;">Hiện tại chưa có mã khuyến mãi nào khả dụng. Vui lòng quay lại sau!</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Xem tất cả danh mục -->
    <div class="modal fade" id="allCategoriesModal" tabindex="-1" role="dialog" aria-labelledby="allCategoriesModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content" style="border-radius: 20px; border: 1px solid rgba(255, 255, 255, 0.08); background: #222831; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);">
                <div class="modal-header" style="border-bottom: 1px solid rgba(255, 255, 255, 0.05); padding: 20px 24px;">
                    <h5 class="modal-title text-white font-weight-bold" id="allCategoriesModalLabel" style="display: flex; align-items: center; gap: 10px;">
                        <i class="fa fa-th-large text-warning" style="font-size: 20px;"></i> Tất cả danh mục món ăn
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity: 0.8; outline: none; background: transparent; border: none;">
                        <span aria-hidden="true" style="font-size: 28px;">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="padding: 24px;">
                    <!-- Search Bar inside Modal -->
                    <div class="input-group mb-4" style="border-radius: 30px; background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); overflow: hidden; padding: 4px 10px;">
                        <div class="input-group-prepend" style="border: none; display: flex; align-items: center;">
                            <span class="input-group-text" style="background: transparent; border: none; color: #ffbe33;">
                                <i class="fa fa-search"></i>
                            </span>
                        </div>
                        <input type="text" id="searchCategoryInput" class="form-control text-white" placeholder="Tìm kiếm nhanh danh mục..." style="background: transparent; border: none; outline: none; box-shadow: none; font-size: 15px;">
                    </div>
                    
                    <!-- Categories Grid -->
                    <div class="row" id="modalCategoriesGrid" style="max-height: 380px; overflow-y: auto; padding: 4px;">
                        <!-- All Categories rendered as beautiful luxury pills -->
                        <div class="col-6 col-sm-4 col-md-3 mb-3 category-pill-item" data-filter="*">
                            <div class="p-3 text-center rounded-lg text-white font-weight-bold" style="background: rgba(255, 190, 51, 0.1); border: 1px solid rgba(255, 190, 51, 0.2); cursor: pointer; transition: all 0.2s ease; border-radius: 12px; font-size: 13.5px;" onmouseover="this.style.background='rgba(255, 190, 51, 0.2)'; this.style.transform='translateY(-2px)';" onmouseout="this.style.background='rgba(255, 190, 51, 0.1)'; this.style.transform='none';">
                                Tất cả món ăn
                            </div>
                        </div>
                        @if(isset($categories) && count($categories) > 0)
                            @foreach($categories as $c)
                                @php
                                    $slug = \Illuminate\Support\Str::slug($c->TenDanhMuc, '-');
                                @endphp
                                <div class="col-6 col-sm-4 col-md-3 mb-3 category-pill-item" data-search-name="{{ strtolower($c->TenDanhMuc) }}" data-filter=".{{ $slug }}">
                                    <div class="p-3 text-center rounded-lg text-white" style="background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.05); cursor: pointer; transition: all 0.2s ease; border-radius: 12px; font-size: 13.5px;" onmouseover="this.style.background='rgba(255, 190, 51, 0.15)'; this.style.borderColor='#ffbe33'; this.style.transform='translateY(-2px)';" onmouseout="this.style.background='rgba(255, 255, 255, 0.03)'; this.style.borderColor='rgba(255, 255, 255, 0.05)'; this.style.transform='none';">
                                        {{ $c->TenDanhMuc }}
                                    </div>
                                </div>
                            @endforeach
                        @else
                            @php
                                $staticCategories = [
                                    ['name' => 'Cơm', 'slug' => 'com'],
                                    ['name' => 'Bún', 'slug' => 'bun'],
                                    ['name' => 'Phở', 'slug' => 'pho'],
                                    ['name' => 'Mì', 'slug' => 'mi'],
                                    ['name' => 'Trà sữa', 'slug' => 'tra-sua'],
                                    ['name' => 'Bánh mì', 'slug' => 'banh-mi'],
                                    ['name' => 'Đồ chay', 'slug' => 'do-chay'],
                                    ['name' => 'Pizza', 'slug' => 'pizza'],
                                    ['name' => 'Burger', 'slug' => 'burger'],
                                    ['name' => 'Lẩu', 'slug' => 'lau'],
                                    ['name' => 'Đồ nướng', 'slug' => 'do-nuong'],
                                    ['name' => 'Tráng miệng', 'slug' => 'trang-mieng']
                                ];
                            @endphp
                            @foreach($staticCategories as $sc)
                                <div class="col-6 col-sm-4 col-md-3 mb-3 category-pill-item" data-search-name="{{ strtolower($sc['name']) }}" data-filter=".{{ $sc['slug'] }}">
                                    <div class="p-3 text-center rounded-lg text-white" style="background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.05); cursor: pointer; transition: all 0.2s ease; border-radius: 12px; font-size: 13.5px;" onmouseover="this.style.background='rgba(255, 190, 51, 0.15)'; this.style.borderColor='#ffbe33'; this.style.transform='translateY(-2px)';" onmouseout="this.style.background='rgba(255, 255, 255, 0.03)'; this.style.borderColor='rgba(255, 255, 255, 0.05)'; this.style.transform='none';">
                                        {{ $sc['name'] }}
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Category Modal JS wireup -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Real-time search inside See All Categories Modal
            const searchCategoryInput = document.getElementById('searchCategoryInput');
            if (searchCategoryInput) {
                searchCategoryInput.addEventListener('input', function() {
                    const query = this.value.toLowerCase().trim();
                    const items = document.querySelectorAll('#modalCategoriesGrid .category-pill-item');
                    
                    items.forEach(item => {
                        const name = item.getAttribute('data-search-name');
                        if (!name) return; // Skip "Tất cả"
                        
                        if (name.includes(query)) {
                            item.style.display = 'block';
                        } else {
                            item.style.display = 'none';
                        }
                    });
                });
            }

            // Handle clicks on Category Pills in the Modal
            const categoryPills = document.querySelectorAll('#modalCategoriesGrid .category-pill-item');
            categoryPills.forEach(pill => {
                pill.addEventListener('click', function() {
                    const filterValue = this.getAttribute('data-filter');
                    
                    // 1. Close Modal
                    $('#allCategoriesModal').modal('hide');
                    
                    // 2. Clear active class on main horizontal tags
                    $('.filters_menu li').removeClass('active');
                    
                    // 3. Find if this category exists in the horizontal list and make it active
                    let mainListItem = $(`.filters_menu li[data-filter="${filterValue}"]`);
                    if (mainListItem.length > 0) {
                        mainListItem.addClass('active');
                        
                        // Scroll horizontal bar to center the active tag
                        const container = document.querySelector('.filters_menu');
                        if (container) {
                            const activeEl = mainListItem[0];
                            const offsetLeft = activeEl.offsetLeft - (container.clientWidth / 2) + (activeEl.clientWidth / 2);
                            container.scrollTo({ left: offsetLeft, behavior: 'smooth' });
                        }
                    } else {
                        // Highlight the "Xem tất cả" button to show a custom category is active
                        $('.see-all-categories-btn').addClass('active');
                    }
                    
                    // 4. Trigger Category filtering with dynamic AJAX API Loader!
                    const categoryName = this.textContent.trim();
                    const filters = (filterValue === '*' || categoryName === 'Tất cả món ăn') ? {} : { category: categoryName };
                    
                    // Clear search input so there's no conflict
                    const searchInput = document.getElementById('searchInput');
                    if (searchInput) searchInput.value = '';
                    
                    loadFoods(1, filters, false);
                    
                    // 5. Smooth scroll down to the food list section
                    const foodSection = document.querySelector('.food_section');
                    if (foodSection) {
                        foodSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                });
            });
        });
    </script>

    <script>
        // Set initial paginator state from Server Side Rendering
        window.initialPagination = {
            current_page: 1,
            last_page: {{ (int)ceil(\App\Models\MonAn::whereIn('DanhMuc', \App\Models\DanhMuc::where('TrangThai', 'Hoạt động')->pluck('TenDanhMuc')->toArray())->where('TrangThai', 'Còn bán')->count() / 12) }},
            per_page: 12,
            total: {{ \App\Models\MonAn::whereIn('DanhMuc', \App\Models\DanhMuc::where('TrangThai', 'Hoạt động')->pluck('TenDanhMuc')->toArray())->where('TrangThai', 'Còn bán')->count() }}
        };
    </script>
    <!-- Load foods from RESTful API -->
    <script src="{{ asset('js/api-food-loader.js') }}?v={{ time() }}"></script>
    
@endsection
