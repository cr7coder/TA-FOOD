@extends('client.layouts.master')

@section('content')
    <!-- Premium Header Hero Banner with Dynamic Gradient -->
    <div class="restaurant-hero-section position-relative py-5 text-white" 
         style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); margin-top: 80px; overflow: hidden; border-bottom: 4px solid #ffbe33;">
        
        <!-- Elegant ambient background pattern -->
        <div class="position-absolute w-100 h-100" style="top: 0; left: 0; background: radial-gradient(circle at 80% 20%, rgba(255, 190, 51, 0.08) 0%, transparent 50%); z-index: 1;"></div>
        
        <div class="container position-relative" style="z-index: 2;">
            <div class="d-flex flex-column flex-md-row align-items-center align-items-md-start">
                
                <!-- Restaurant Avatar/Badge -->
                @php
                    $firstLetter = mb_substr($restaurant->TenNhaHang, 0, 1, 'UTF-8');
                @endphp
                <div class="restaurant-avatar-large d-flex align-items-center justify-content-center text-white font-weight-bold rounded-circle shadow-lg mb-4 mb-md-0 mr-md-4" 
                     style="width: 100px; height: 100px; min-width: 100px; background: rgba(255, 255, 255, 0.1); border: 3px solid rgba(255, 190, 51, 0.6); font-size: 42px; text-shadow: 0 4px 10px rgba(0,0,0,0.3); backdrop-filter: blur(10px);">
                    {{ $firstLetter }}
                </div>
                
                <!-- Restaurant Main Details -->
                <div class="text-center text-md-left flex-grow-1">
                    <div class="d-flex flex-column flex-md-row align-items-center justify-content-md-between">
                        <div>
                            <h1 class="display-5 font-weight-bold mb-2 text-white" style="letter-spacing: -0.5px; font-size: 2.5rem; text-shadow: 0 2px 4px rgba(0,0,0,0.5);">
                                {{ $restaurant->TenNhaHang }}
                            </h1>
                            <span class="badge badge-warning py-2 px-3 font-weight-bold shadow-sm" style="font-size: 13.5px; background-color: #ffbe33; color: #1e293b; border-radius: 30px;">
                                <i class="fa fa-utensils mr-2"></i> {{ $foods->count() }} món ăn chất lượng
                            </span>
                        </div>
                        
                        <!-- Back button ghost style -->
                        <div class="mt-3 mt-md-0">
                            <a href="{{ route('foods.index') }}" class="btn btn-outline-light rounded-pill px-4 py-2 text-uppercase font-weight-bold shadow-sm back-ghost-btn" 
                               style="font-size: 13px; border-color: rgba(255,255,255,0.4); transition: all 0.3s ease;">
                                <i class="fa fa-arrow-left mr-2"></i> Quay lại
                            </a>
                        </div>
                    </div>
                    
                    <!-- Meta info list -->
                    <div class="mt-4 pt-3 border-top border-secondary d-flex flex-column flex-sm-row flex-wrap align-items-center justify-content-center justify-content-md-start text-white-50" style="font-size: 14.5px; gap: 20px;">
                        <div>
                            <i class="fa fa-map-marker-alt text-warning mr-2"></i> 
                            <span>{{ $restaurant->DiaChi }}</span>
                        </div>
                        @if($restaurant->SoDienThoai)
                            <div>
                                <i class="fa fa-phone-alt text-warning mr-2"></i> 
                                <span>{{ $restaurant->SoDienThoai }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Foods Grid Section -->
    <section class="food_section py-5 bg-light" style="min-height: 50vh;">
        <div class="container">
            
            <div class="heading_container heading_center mb-5">
                <h2 class="font-weight-bold text-dark position-relative pb-2" style="font-size: 2rem;">
                    Thực đơn trứ danh
                    <div class="position-absolute bg-warning" style="width: 60px; height: 4px; bottom: 0; left: 50%; transform: translateX(-50%); border-radius: 2px;"></div>
                </h2>
                <p class="text-muted mt-3 font-italic">Tuyển chọn các món ăn nóng hổi, vệ sinh và giao hàng siêu tốc từ quán</p>
            </div>

            <div class="row">
                @forelse($foods as $food)
                    @php
                        $imageSrc = $food->HinhAnh ? asset('images/' . $food->HinhAnh) : asset('images/no-image.png');
                    @endphp
                    <div class="col-sm-6 col-lg-4 mb-4">
                        <div class="card h-100 border-0 shadow-sm premium-food-card" 
                             style="border-radius: 16px; overflow: hidden; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); border: 1px solid rgba(0,0,0,0.04) !important; background: #ffffff;">
                            
                            <!-- Food image container with zoom effect -->
                            <div class="position-relative overflow-hidden food-image-box" style="height: 200px;">
                                <img src="{{ $imageSrc }}" 
                                     alt="{{ $food->TenMonAn }}" 
                                     class="w-100 h-100" 
                                     style="object-fit: cover; transition: transform 0.5s ease;">
                                
                                <!-- Category Tag -->
                                <span class="badge badge-danger position-absolute py-1 px-3 shadow" 
                                      style="top: 15px; left: 15px; font-size: 11px; border-radius: 20px; font-weight: 600; background-color: #dc3545;">
                                    {{ $food->DanhMuc }}
                                </span>
                            </div>
                            
                            <!-- Food Details -->
                            <div class="card-body d-flex flex-column p-4">
                                <h5 class="card-title font-weight-bold text-dark mb-2 text-truncate-2" style="font-size: 16px; line-height: 1.4; height: 45px;">
                                    {{ $food->TenMonAn }}
                                </h5>
                                
                                <p class="card-text text-muted mb-4" style="font-size: 13px; line-height: 1.5; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; height: 38px;">
                                    {{ $food->MoTa ?: 'Món ăn thơm ngon, bổ dưỡng được chế biến sạch sẽ, chuẩn vị truyền thống.' }}
                                </p>
                                
                                <!-- Footer with Price & dynamic Add to Cart button -->
                                <div class="mt-auto d-flex justify-content-between align-items-center pt-3 border-top" style="border-top-color: #f1f5f9 !important;">
                                    <span class="font-weight-bold text-primary" style="font-size: 18px; color: #ff6b35 !important; font-weight: 800;">
                                        {{ number_format($food->Gia, 0, ',', '.') }} đ
                                    </span>
                                    
                                    <button type="button" 
                                            class="btn btn-warning add-to-cart-btn rounded-pill px-4 py-2 d-flex align-items-center" 
                                            data-id="{{ $food->MaMonAn }}" 
                                            data-name="{{ $food->TenMonAn }}" 
                                            data-price="{{ $food->Gia }}" 
                                            data-image="{{ $imageSrc }}"
                                            style="background-color: #ffbe33; border: none; color: #1e293b; font-size: 13.5px; font-weight: 700; transition: all 0.2s ease;">
                                        <i class="fa fa-shopping-cart mr-2"></i> Thêm giỏ
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <div class="mb-4">
                            <i class="fa fa-utensils fa-4x text-muted opacity-50"></i>
                        </div>
                        <h4 class="text-muted">Nhà hàng này chưa đăng bán món ăn nào hoặc đã hết hàng.</h4>
                        <p class="text-muted-50">Quý khách vui lòng tham khảo các nhà hàng khác trên TA-FOOD!</p>
                        <a href="{{ route('foods.index') }}" class="btn btn-warning mt-3 rounded-pill px-4">
                            Xem các quán ngon khác
                        </a>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Custom CSS styles for premium page aesthetics -->
    <style>
        .premium-food-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04) !important;
        }

        .premium-food-card:hover .food-image-box img {
            transform: scale(1.08);
        }

        .text-truncate-2 {
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }

        .back-ghost-btn:hover {
            background-color: #ffbe33 !important;
            border-color: #ffbe33 !important;
            color: #1e293b !important;
            transform: translateX(-4px);
        }

        /* Styling for the Add to Cart animation to synchronize perfectly with page styling */
        .add-to-cart-btn {
            box-shadow: 0 4px 6px -1px rgba(255, 190, 51, 0.2);
        }
        
        .add-to-cart-btn:hover {
            background-color: #e0a800 !important;
            transform: scale(1.05);
            box-shadow: 0 10px 15px -3px rgba(255, 190, 51, 0.3) !important;
        }

        .add-to-cart-btn.adding {
            animation: addToCartPulse 0.5s ease;
        }

        @keyframes addToCartPulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.2); background-color: #28a745 !important; color: white !important; }
            100% { transform: scale(1); }
        }
    </style>

    <!-- Connect standard API-based giỏ hàng scripts -->
    @include('client.partials._cart-scripts')
@endsection