@extends('client.layouts.master')

@section('title', 'Món ăn yêu thích')

@section('content')
    <div class="hero_area">
        <div class="bg-box">
            <img src="{{ asset('images/bg2.jpg') }}" alt="">
        </div>
        @include('client.partials._header')
    </div>

<style>
    /* Optimize header area height on PC to prevent excessive scrolling */
    @media (min-width: 992px) {
        .hero_area {
            min-height: 125px !important;
            height: 125px !important;
        }
        .hero_area .bg-box {
            height: 125px !important;
        }
    }

    /* Premium Page Background */
    body {
        background: linear-gradient(135deg, #f0f2f5 0%, #e8ecf1 100%) !important;
        min-height: 100vh;
    }

    /* Main Container */
    .favorites-container {
        max-width: 1000px;
        margin: 40px auto;
        padding: 40px 32px;
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border-radius: 24px;
        border: 1px solid rgba(255, 255, 255, 0.6);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.05);
        min-height: calc(100vh - 240px);
    }

    #detailBackButton:hover {
        background-color: #e69d00 !important;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(255, 190, 51, 0.35) !important;
    }

    @media (max-width: 576px) {
        #detailBackButton {
            padding: 6px 12px !important;
            font-size: 12px !important;
            gap: 5px !important;
            justify-content: center !important;
        }
    }

    /* Title Centered Perfectly with CSS Underline Indicator */
    .page-title-wrapper {
        text-align: center;
        margin-bottom: 40px;
    }

    .page-title {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        font-size: 32px;
        font-weight: 800;
        color: #222831;
        letter-spacing: -0.5px;
        position: relative;
        display: inline-block;
    }

    .page-title::after {
        content: '';
        position: absolute;
        bottom: -8px;
        left: 0;
        right: 0;
        margin: 0 auto;
        width: 80px;
        height: 4px;
        background: linear-gradient(90deg, #ffbe33, #ff6900);
        border-radius: 2px;
    }

    /* Filters & Navigation Tab Panel */
    .filters-panel {
        background: white;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
        border: 1px solid rgba(0, 0, 0, 0.03);
        margin-bottom: 30px;
    }

    .search-filter-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 20px;
        margin-bottom: 20px;
    }

    @media (min-width: 768px) {
        .search-filter-grid {
            grid-template-columns: 1fr;
        }
    }

    .search-input-wrapper {
        position: relative;
        width: 100%;
    }

    .search-input-wrapper i {
        position: absolute;
        left: 18px;
        top: 0;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #a0aec0;
        font-size: 16px;
        pointer-events: none;
        margin: 0;
    }

    .search-input-wrapper input {
        width: 100%;
        padding: 12px 16px 12px 48px;
        border-radius: 12px;
        border: 1.5px solid #e2e8f0;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        font-size: 14.5px;
        font-weight: 500;
        transition: all 0.25s ease;
        outline: none;
    }

    .search-input-wrapper input:focus {
        border-color: #ff6900;
        box-shadow: 0 0 0 3px rgba(255, 105, 0, 0.1);
    }

    /* Horizontal Category Status Tabs */
    .status-tabs-container {
        display: flex;
        justify-content: center;
        gap: 8px;
        overflow-x: auto;
        padding-bottom: 4px;
        scrollbar-width: none;
    }

    .status-tabs-container::-webkit-scrollbar {
        display: none;
    }

    .status-tab {
        padding: 10px 20px;
        background: #f7fafc;
        border: 1px solid #edf2f7;
        border-radius: 30px;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        font-size: 13.5px;
        font-weight: 700;
        color: #4a5568;
        cursor: pointer;
        transition: all 0.25s ease;
        white-space: nowrap;
    }

    .status-tab:hover {
        background: #edf2f7;
        color: #ff6900;
    }

    .status-tab.active {
        background: linear-gradient(135deg, #ffbe33 0%, #ff6900 100%);
        color: white;
        border-color: transparent;
        box-shadow: 0 4px 15px rgba(255, 105, 0, 0.15);
    }

    /* Empty state */
    .no-favorites {
        text-align: center;
        padding: 80px 20px;
    }

    .empty-state-illustration {
        font-size: 72px;
        color: #fecdd3;
        margin-bottom: 24px;
        animation: heartbeat 1.5s ease-in-out infinite;
    }

    @keyframes heartbeat {
        0% { transform: scale(1); }
        14% { transform: scale(1.12); }
        28% { transform: scale(1); }
        42% { transform: scale(1.12); }
        70% { transform: scale(1); }
    }

    .empty-state-title {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        font-size: 22px;
        font-weight: 800;
        color: #2d3748;
        margin-bottom: 12px;
    }

    .no-favorites-text {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        font-size: 15px;
        color: #718096;
        margin-bottom: 28px;
        max-width: 480px;
        margin-left: auto;
        margin-right: auto;
        line-height: 1.6;
    }

    .btn-primary-custom {
        background: linear-gradient(135deg, #ffbe33 0%, #ff6900 100%);
        border: none;
        padding: 12px 36px;
        border-radius: 12px;
        color: white !important;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        font-size: 15px;
        font-weight: 700;
        text-decoration: none !important;
        cursor: pointer;
        display: inline-block;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(255, 105, 0, 0.15);
    }

    .btn-primary-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(255, 105, 0, 0.3);
    }

    /* Card removal animation */
    .fade-out-scale {
        animation: fadeOutScale 0.45s cubic-bezier(0.6, -0.28, 0.735, 0.045) forwards;
    }

    @keyframes fadeOutScale {
        0% { opacity: 1; transform: scale(1); }
        100% { opacity: 0; transform: scale(0.8); height: 0; margin-top: 0; margin-bottom: 0; padding-top: 0; padding-bottom: 0; }
    }
</style>

<div class="container pb-5">
    <div class="favorites-container">
        <!-- Floating Back Button -->
        <a href="{{ route('foods.index') }}" id="detailBackButton" class="btn btn-warning px-4 py-2 font-weight-bold shadow-sm d-flex align-items-center" 
           style="border-radius: 25px; background-color: #ffbe33; border: none; color: #1e293b; transition: all 0.2s ease; font-size: 13.5px; gap: 8px; font-weight: 700; text-decoration: none; width: fit-content; margin-bottom: 30px;">
            <i class="fa fa-arrow-left"></i> Quay lại
        </a>

        <!-- Page Title -->
        <div class="page-title-wrapper">
            <h1 class="page-title">Món ăn yêu thích</h1>
        </div>

        <!-- Filters panel -->
        <div class="filters-panel">
            <div class="search-filter-grid">
                <div class="search-input-wrapper">
                    <i class="fa fa-search"></i>
                    <input type="text" id="favSearchInput" placeholder="Tìm kiếm món ăn yêu thích của bạn...">
                </div>
            </div>

            <!-- Categories Tabs -->
            <div class="status-tabs-container">
                <div class="status-tab active" data-filter="*">Tất cả</div>
                @foreach($categories as $cat)
                    <div class="status-tab" data-filter=".{{ \Illuminate\Support\Str::slug($cat->TenDanhMuc, '-') }}">{{ $cat->TenDanhMuc }}</div>
                @endforeach
            </div>
        </div>

        <!-- Food grid section -->
        <div class="food_section">
            <div class="row" id="favoritesGrid">
                @forelse($foods as $food)
                    <div class="col-6 col-sm-6 col-lg-4 food-grid-item all {{ $food->loai_mon_class }} {{ $food->TrangThai == 'Còn bán' ? 'con-ban' : 'ngung-ban' }}">
                        <div class="box" data-detail-url="{{ route('food.detail', $food->MaMonAn) }}" style="cursor: pointer;">
                            <div>
                                <div class="img-box position-relative">
                                    <img src="{{ $food->HinhAnh ? asset('images/' . $food->HinhAnh) : asset('images/no-image.png') }}"
                                        alt="{{ $food->TenMonAn }}" loading="lazy" style="transition: transform 0.5s cubic-bezier(0.25, 0.46, 0.45, 0.94) !important;">
                                    
                                    <!-- Heart Favorite Toggle Button -->
                                    <button type="button" class="heart-fav-btn remove-fav-btn" 
                                            data-id="{{ $food->MaMonAn }}" 
                                            title="Xóa khỏi yêu thích"
                                            style="position: absolute; top: 15px; right: 15px; width: 36px; height: 36px; background: rgba(255, 255, 255, 0.9); border: none; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; z-index: 10; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15); transition: all 0.2s ease;"
                                            onmouseover="this.style.transform='scale(1.1)'; this.style.boxShadow='0 6px 15px rgba(239, 68, 68, 0.3)';"
                                            onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='0 4px 10px rgba(0, 0, 0, 0.15)';">
                                        <i class="fa fa-heart text-danger" style="font-size: 16px; transition: transform 0.2s ease;"></i>
                                    </button>

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
                                                    data-image="{{ $food->HinhAnh ? asset('images/' . $food->HinhAnh) : asset('images/no-image.png') }}"
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
                @empty
                    <!-- Empty State View -->
                    <div class="col-12" id="emptyState">
                        <div class="no-favorites">
                            <div class="empty-state-illustration">
                                <i class="fa fa-heart"></i>
                            </div>
                            <h3 class="empty-state-title">Danh sách yêu thích trống trơn</h3>
                            <p class="no-favorites-text">
                                Hãy lướt xem thực đơn phong phú của TAFOOD và nhấn tim lưu lại những món ăn bạn thích để đặt nhanh cho lần sau nhé!
                            </p>
                            <a href="{{ route('foods.index') }}" class="btn-primary-custom">
                                <i class="fa fa-utensils mr-2"></i> Khám phá thực đơn
                            </a>
                        </div>
                    </div>
                @endforelse
            </div>
            
            <!-- Sleek pagination -->
            <div class="pagination-wrapper mt-4 d-flex justify-content-center">
                {{ $foods->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const searchInput = document.getElementById("favSearchInput");
        const categoryTabs = document.querySelectorAll(".status-tab");
        const gridItems = document.querySelectorAll(".food-grid-item");
        let emptyStateEl = document.getElementById("emptyState");
        if (!emptyStateEl) {
            emptyStateEl = document.createElement("div");
            emptyStateEl.id = "emptyState";
            emptyStateEl.className = "col-12 d-none";
            emptyStateEl.innerHTML = `
                <div class="no-favorites">
                    <div class="empty-state-illustration" style="font-size: 60px; color: #cbd5e1; margin-bottom: 20px;">
                        <i class="fa fa-search"></i>
                    </div>
                    <h3 class="empty-state-title">Không tìm thấy món ăn</h3>
                    <p class="no-favorites-text">
                        Không có món ăn nào phù hợp với tìm kiếm hoặc danh mục này.
                    </p>
                </div>`;
            const favoritesGrid = document.getElementById("favoritesGrid");
            if (favoritesGrid) favoritesGrid.appendChild(emptyStateEl);
        }

        // Check if list is empty dynamically
        function checkEmptyState() {
            const visibleItems = Array.from(gridItems).filter(item => !item.classList.contains('d-none') && !item.classList.contains('fade-out-scale'));
            if (visibleItems.length === 0) {
                emptyStateEl.classList.remove('d-none');
                const pagination = document.querySelector(".pagination-wrapper");
                if (pagination) pagination.style.display = "none";
            } else {
                emptyStateEl.classList.add('d-none');
                const pagination = document.querySelector(".pagination-wrapper");
                if (pagination) pagination.style.display = "flex";
            }
        }

        // Live client-side Search and Filter logic
        function filterItems() {
            const query = searchInput.value.toLowerCase().trim();
            const activeTab = document.querySelector(".status-tab.active");
            const filterClass = activeTab.getAttribute("data-filter");

            gridItems.forEach(item => {
                const title = item.querySelector(".detail-box h5").textContent.toLowerCase();
                const matchesSearch = title.includes(query);
                const matchesCategory = (filterClass === "*" || item.classList.contains(filterClass.substring(1)));

                if (matchesSearch && matchesCategory) {
                    item.classList.remove("d-none");
                } else {
                    item.classList.add("d-none");
                }
            });
            checkEmptyState();
        }

        if (searchInput) {
            searchInput.addEventListener("input", filterItems);
        }

        categoryTabs.forEach(tab => {
            tab.addEventListener("click", function() {
                categoryTabs.forEach(t => t.classList.remove("active"));
                this.classList.add("active");
                filterItems();
            });
        });

        // AJAX Toggle Favorite removal from wishlist page
        $(document).on('click', '.remove-fav-btn', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const btn = $(this);
            const foodId = btn.data('id');
            const gridItem = btn.closest('.food-grid-item');

            btn.prop('disabled', true);

            $.ajax({
                url: `/favorites/${foodId}/toggle`,
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        // Success toast
                        const msg = response.message || 'Đã xóa khỏi danh sách yêu thích!';
                        if (window.showToast) {
                            showToast(msg);
                        } else if (window.showAlert) {
                            showAlert('success', msg);
                        }
                        
                        // Add fade out animation
                        gridItem.addClass('fade-out-scale');
                        
                        setTimeout(() => {
                            gridItem.remove();
                            checkEmptyState();
                        }, 450);
                    } else {
                        btn.prop('disabled', false);
                        if (window.showAlert) {
                            showAlert('danger', 'Đã có lỗi xảy ra. Vui lòng thử lại!');
                        }
                    }
                },
                error: function(xhr) {
                    btn.prop('disabled', false);
                    if (window.showAlert) {
                        showAlert('danger', 'Đã có lỗi xảy ra. Vui lòng thử lại!');
                    }
                }
            });
        });
    });
</script>
@endsection
