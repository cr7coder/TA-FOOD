@extends('client.layouts.master')

@section('content')




    <div class="hero_area">
        <div class="bg-box">
            <img src="{{ asset('images/bg2.jpg') }}" alt="">
        </div>
        @include('client.partials._header')
    </div>

    <section class="food_detail_section layout_padding">
        <div class="container">
            <!-- Breadcrumb & Back Button -->
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap" style="gap: 15px;">
                <nav aria-label="breadcrumb" class="mb-0">
                    <ol class="breadcrumb mb-0" style="background: transparent; padding: 0;">
                        <li class="breadcrumb-item"><a href="{{ route('foods.index') }}">Trang chủ</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ $food->TenMonAn }}</li>
                    </ol>
                </nav>
                <a href="/" id="detailBackButton" class="btn btn-warning px-4 py-2 font-weight-bold shadow-sm d-flex align-items-center" 
                   style="border-radius: 25px; background-color: #ffbe33; border: none; color: #1e293b; transition: all 0.2s ease; font-size: 13.5px; gap: 8px;">
                    <i class="fa fa-arrow-left"></i> Quay lại
                </a>
            </div>

            <div class="row d-flex align-items-stretch">
                <!-- Food Media Gallery -->
                <div class="col-lg-6 mb-4">
                    <div class="food-image-container" style="padding: 20px; position: relative; background-color: #f1f2f3;">
                        <!-- Favorite Heart Toggle Button -->
                        @auth
                            <button type="button" class="heart-fav-btn toggle-fav-btn" 
                                    data-id="{{ $food->MaMonAn }}" 
                                    title="{{ $food->is_favorite ? 'Xóa khỏi yêu thích' : 'Thêm vào yêu thích' }}"
                                    style="position: absolute; top: 15px; right: 15px; width: 42px; height: 42px; background: rgba(255, 255, 255, 0.9); border: none; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; z-index: 10; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15); transition: all 0.2s ease;">
                                <i class="{{ $food->is_favorite ? 'fa fa-heart text-danger' : 'far fa-heart text-muted' }}" style="font-size: 20px; transition: transform 0.2s ease;"></i>
                            </button>
                        @else
                            <button type="button" class="heart-fav-btn require-login-fav-btn" 
                                    title="Đăng nhập để lưu yêu thích"
                                    style="position: absolute; top: 15px; right: 15px; width: 42px; height: 42px; background: rgba(255, 255, 255, 0.9); border: none; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; z-index: 10; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15); transition: all 0.2s ease;">
                                <i class="far fa-heart text-muted" style="font-size: 20px;"></i>
                            </button>
                        @endauth

                        {{-- Build media list: main image + secondary images from ThuVienAnh --}}
                        @php
                            $mediaList = [];
                            $mediaList[] = [
                                'type'  => 'image',
                                'url'   => $food->hinh_anh_url,
                                'label' => 'Ảnh chính',
                            ];

                            foreach ($food->thu_vien_anh_urls as $imgUrl) {
                                $mediaList[] = [
                                    'type'  => 'image',
                                    'url'   => $imgUrl,
                                    'label' => 'Ảnh phụ',
                                ];
                            }
                        @endphp

                        <!-- Gallery Track -->
                        <div id="galleryTrack" style="position: relative; width: 100%; overflow: hidden; border-radius: 16px 16px 0 0;">
                            @php
                                $foodMediaPaths = [];
                                $foodMediaPaths[] = $food->hinh_anh_url;
                                foreach ($food->thu_vien_anh_urls as $imgUrl) {
                                    $foodMediaPaths[] = $imgUrl;
                                }
                            @endphp

                            @foreach($mediaList as $idx => $media)
                                <div class="gallery-slide" data-index="{{ $idx }}"
                                     style="display: {{ $idx === 0 ? 'flex' : 'none' }}; position: relative; height: 100%; align-items: center; justify-content: center; cursor: pointer;"
                                     data-media="{{ json_encode($foodMediaPaths) }}" onclick="openCommentGallery(this, {{ $idx }})">
                                     <img src="{{ $media['url'] }}"
                                          alt="{{ $media['label'] }}"
                                          style="width: 100%; height: 380px; object-fit: contain; display: block; border-radius: 12px;">
                                </div>
                            @endforeach

                            @if(count($mediaList) > 1)
                                {{-- Prev button --}}
                                <button onclick="galleryNav(-1)" class="gallery-nav-btn" id="galleryPrev"
                                        style="position: absolute; top: 50%; left: 12px; transform: translateY(-50%);
                                               width: 40px; height: 40px; border-radius: 50%; border: none;
                                               background: rgba(255,255,255,0.9); color: #1e293b;
                                               font-size: 16px; cursor: pointer; z-index: 10;
                                               box-shadow: 0 4px 12px rgba(0,0,0,0.2);
                                               transition: all 0.2s ease; display: flex; align-items: center; justify-content: center;">
                                    <i class="fa fa-chevron-left"></i>
                                </button>

                                {{-- Next button --}}
                                <button onclick="galleryNav(1)" class="gallery-nav-btn" id="galleryNext"
                                        style="position: absolute; top: 50%; right: 12px; transform: translateY(-50%);
                                               width: 40px; height: 40px; border-radius: 50%; border: none;
                                               background: rgba(255,255,255,0.9); color: #1e293b;
                                               font-size: 16px; cursor: pointer; z-index: 10;
                                               box-shadow: 0 4px 12px rgba(0,0,0,0.2);
                                               transition: all 0.2s ease; display: flex; align-items: center; justify-content: center;">
                                    <i class="fa fa-chevron-right"></i>
                                </button>
                            @endif
                        </div>



                    </div>
                </div>

                <div class="col-lg-6 mb-4">
                    <div class="food-info">
                        <h1 class="food-title">{{ $food->TenMonAn }}</h1>

                        <div class="rating-overview mb-3">
                            <div class="stars">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= floor($food->diem_trung_binh))
                                        <i class="fa fa-star text-warning"></i>
                                    @elseif($i == ceil($food->diem_trung_binh) && $food->diem_trung_binh - floor($food->diem_trung_binh) >= 0.5)
                                        <i class="fa fa-star-half-alt text-warning"></i>
                                    @else
                                        <i class="fa fa-star text-muted"></i>
                                    @endif
                                @endfor
                                <span class="rating-text ms-2">
                                    {{ number_format($food->diem_trung_binh, 1) }}/5
                                    ({{ $food->tong_binh_luan }} đánh giá)
                                </span>
                            </div>
                        </div>

                        <div class="price mb-3">
                            <h3 class="text-warning">{{ number_format($food->Gia, 0, ',', '.') }} đ</h3>
                        </div>

                        @if($food->MoTa)
                            <div class="description mb-4">
                                <h5>Mô tả:</h5>
                                <p>{{ $food->MoTa }}</p>
                            </div>
                        @endif

                        <div class="restaurant-info mb-4">
                            <h5 class="mb-2" style="font-weight: 700; font-size: 15px; color: #1e293b;">Nhà hàng:</h5>
                            <div class="d-flex align-items-center">
                                <div class="restaurant-avatar mr-3 me-3" style="flex-shrink: 0;">
                                    {{ substr($food->nhaHang->TenNhaHang, 0, 1) }}
                                </div>
                                <div class="d-flex flex-column justify-content-center">
                                    <strong class="d-block mb-1" style="color: #1e293b; font-size: 15px; font-weight: 700; line-height: 1.2;">{{ $food->nhaHang->TenNhaHang }}</strong>
                                    <p class="text-muted mb-0" style="font-size: 13px; line-height: 1.3;">{{ $food->nhaHang->DiaChi }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="food-actions">
                            @if($food->TrangThai == 'Còn bán')
                                <button type="button" class="btn btn-warning btn-lg add-to-cart-btn"
                                    data-id="{{ $food->MaMonAn }}" data-name="{{ $food->TenMonAn }}"
                                    data-price="{{ $food->Gia }}"
                                    data-image="{{ $food->HinhAnh ? asset('images/' . $food->HinhAnh) : asset('images/no-image.png') }}">
                                    <i class="fa fa-shopping-cart me-2"></i>
                                    Thêm vào giỏ hàng
                                </button>
                            @else
                                <button class="btn btn-secondary btn-lg" disabled>
                                    <i class="fa fa-times me-2"></i>
                                    Ngừng bán
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reviews Section -->
            {{-- <div class="reviews-section mt-5">
                <div class="row">
                    <div class="col-lg-6">
                        <h3 class="mb-4">Đánh giá & Bình luận</h3>

                        @if(Auth::check())
                        <!-- Review Form -->
                        <div class="review-form mb-5">
                            <h5>Viết đánh giá của bạn</h5>
                            <form action="{{ route('food.binh-luan', $food->MaMonAn) }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf

                                <!-- Star Rating -->
                                <div class="form-group mb-3">
                                    <label>Đánh giá sao:</label>
                                    <div class="star-rating">
                                        @for($i = 5; $i >= 1; $i--)
                                        <input type="radio" name="diem_danh_gia" value="{{ $i }}" id="star{{ $i }}"
                                            required>
                                        <label for="star{{ $i }}" class="star">★</label>
                                        @endfor
                                    </div>
                                    @error('diem_danh_gia')
                                    <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <!-- Comment -->
                                <div class="form-group mb-3">
                                    <label for="noi_dung">Bình luận:</label>
                                    <textarea name="noi_dung" id="noi_dung" class="form-control" rows="4"
                                        placeholder="Chia sẻ trải nghiệm của bạn về món ăn này..."
                                        required>{{ old('noi_dung') }}</textarea>
                                    @error('noi_dung')
                                    <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <!-- Image Upload -->
                                <div class="form-group mb-3">
                                    <label for="hinh_anh">Ảnh (không bắt buộc):</label>
                                    <input type="file" name="hinh_anh" id="hinh_anh" class="form-control" accept="image/*">
                                    @error('hinh_anh')
                                    <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-paper-plane me-2"></i>
                                    Gửi đánh giá
                                </button>
                            </form>
                        </div>
                        @else
                        <div class="alert alert-info">
                            <i class="fa fa-info-circle me-2"></i>
                            <a href="{{ route('login') }}">Đăng nhập</a> để viết đánh giá
                        </div>
                        @endif
                    </div> --}}

                    <div class="col-12 mt-5" id="reviews-section">
                        <!-- Reviews Section Title -->
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h3 class="reviews-title mb-0">
                                <i class="fas fa-comments text-warning me-2"></i>
                                Đánh giá & Bình luận từ khách hàng
                            </h3>
                            @if($food->binhLuans->count() > 0)
                                <button type="button" class="btn btn-sm" data-toggle="modal" data-bs-toggle="modal" data-target="#allReviewsModal" data-bs-target="#allReviewsModal"
                                        style="background-color: transparent; color: #ffbe33; font-weight: 600; border: 1.5px solid #ffbe33; border-radius: 20px; padding: 6px 18px; transition: all 0.2s ease;"
                                        onmouseover="this.style.backgroundColor='#ffbe33'; this.style.color='#1e293b';"
                                        onmouseout="this.style.backgroundColor='transparent'; this.style.color='#ffbe33';">
                                    Xem tất cả ({{ $food->binhLuans->count() }}) <i class="fas fa-angle-right ms-1"></i>
                                </button>
                            @endif
                        </div>

                        <!-- Reviews List (Horizontal Cards Carousel) -->
                        <div class="reviews-list">
                            @if($food->binhLuans->count() > 0)
                                {{-- Outer wrapper provides padding for the arrow buttons --}}
                                <div style="position: relative; padding: 0 44px; display: inline-block; width: fit-content; max-width: 100%;">

                                    @if($food->binhLuans->count() > 1)
                                        {{-- Prev arrow (sits in left padding zone) --}}
                                        <button id="reviewPrev" onclick="reviewsNav(-1)"
                                                style="position: absolute; top: 50%; left: 0; transform: translateY(-50%);
                                                       width: 36px; height: 36px; border-radius: 50%;
                                                       border: 1.5px solid #e2e8f0; background: #ffffff; color: #64748b;
                                                       cursor: pointer; box-shadow: 0 3px 10px rgba(0,0,0,0.08);
                                                       font-size: 13px; transition: all 0.2s ease;
                                                       display: flex; align-items: center; justify-content: center; z-index: 5;">
                                            <i class="fa fa-chevron-left"></i>
                                        </button>

                                        {{-- Next arrow (sits in right padding zone) --}}
                                        <button id="reviewNext" onclick="reviewsNav(1)"
                                                style="position: absolute; top: 50%; right: 0; transform: translateY(-50%);
                                                       width: 36px; height: 36px; border-radius: 50%;
                                                       border: 1.5px solid #e2e8f0; background: #ffffff; color: #64748b;
                                                       cursor: pointer; box-shadow: 0 3px 10px rgba(0,0,0,0.08);
                                                       font-size: 13px; transition: all 0.2s ease;
                                                       display: flex; align-items: center; justify-content: center; z-index: 5;">
                                            <i class="fa fa-chevron-right"></i>
                                        </button>
                                    @endif

                                    <div class="reviews-horizontal-container {{ $food->binhLuans->count() === 1 ? 'single-review-mode' : '' }}" id="reviewsContainer">
                                        @foreach($food->binhLuans as $binhLuan)
                                        <div class="review-card-item" id="review-{{ $binhLuan->id }}">
                                                <div class="review-header d-flex justify-content-between align-items-start mb-2">
                                                <div class="reviewer-info d-flex align-items-center">
                                                    <div class="reviewer-avatar mr-3 me-3" style="flex-shrink: 0;">
                                                        {{ substr($binhLuan->nguoiDung->HoTen, 0, 1) }}
                                                    </div>
                                                    <div>
                                                        <strong class="reviewer-name">{{ $binhLuan->nguoiDung->HoTen }}</strong>
                                                        <div class="review-stars">
                                                            @for($i = 1; $i <= 5; $i++)
                                                                @if($i <= $binhLuan->diem_danh_gia)
                                                                    <i class="fa fa-star text-warning"></i>
                                                                @else
                                                                    <i class="fa fa-star text-muted"></i>
                                                                @endif
                                                            @endfor
                                                        </div>
                                                    </div>
                                                </div>
                                                <small class="review-time text-muted">{{ $binhLuan->created_at->diffForHumans() }}</small>
                                            </div>

                                            <div class="review-body">
                                                <p class="review-text mb-3" style="font-size: 14.5px; color: #334155 !important; line-height: 1.6; margin-top: 10px;">
                                                    {{ $binhLuan->noi_dung }}
                                                </p>

                                                @if($binhLuan->hinh_anh)
                                                    @php
                                                        $decoded = json_decode($binhLuan->hinh_anh, true);
                                                        $mediaList = is_array($decoded) ? $decoded : [$binhLuan->hinh_anh];
                                                        $maxDisplay = 3;
                                                        $totalMedia = count($mediaList);
                                                    @endphp
                                                    <div class="review-media-gallery mt-2 d-flex flex-wrap" style="gap: 8px;">
                                                        @foreach(array_slice($mediaList, 0, $maxDisplay) as $idx => $mediaPath)
                                                            @php
                                                                $ext = strtolower(pathinfo($mediaPath, PATHINFO_EXTENSION));
                                                                $isVideo = in_array($ext, ['mp4', 'mov', 'avi']);
                                                                $isLast = $idx == ($maxDisplay - 1);
                                                                $hasMore = $totalMedia > $maxDisplay;
                                                            @endphp
                                                            <div class="review-media-item" style="width: 85px; height: 85px; border-radius: 8px; overflow: hidden; border: 1px solid #e2e8f0; box-shadow: 0 2px 4px rgba(0,0,0,0.02); background: #f8fafc; cursor: pointer; position: relative;"
                                                                 data-media="{{ json_encode($mediaList) }}" onclick="openCommentGallery(this, {{ $idx }})">
                                                                @php
                                                                    $mediaUrl = filter_var($mediaPath, FILTER_VALIDATE_URL) ? $mediaPath : asset('images/' . $mediaPath);
                                                                @endphp
                                                                @if($isVideo)
                                                                    <video src="{{ $mediaUrl }}" style="width: 100%; height: 100%; object-fit: cover;"></video>
                                                                @else
                                                                    <img src="{{ $mediaUrl }}" alt="Review media" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.2s ease;" onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'">
                                                                @endif
                                                                
                                                                @if($hasMore && $isLast)
                                                                    <div class="more-media-overlay" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); color: white; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 18px; z-index: 10;">
                                                                        +{{ $totalMedia - $maxDisplay }}
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endif

                                                @if($binhLuan->phan_hoi)
                                                    <div class="seller-reply mt-2 p-2 rounded">
                                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                                            <span class="small fw-bold text-warning" style="font-size: 0.75rem;"><i class="fas fa-store me-1"></i> Chủ quán:</span>
                                                        </div>
                                                        <div class="small text-dark italic" style="font-style: italic; font-size: 0.80rem; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; text-overflow: ellipsis;">"{{ $binhLuan->phan_hoi }}"</div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>{{-- end reviews-horizontal-container --}}

                                </div>{{-- end padded wrapper --}}
                            @else
                                <div class="text-center py-5 rounded-3" style="background: #f8fafc; border: 1px solid rgba(0, 0, 0, 0.05);">
                                    <i class="far fa-comments fa-3x mb-3 text-muted" style="opacity: 0.6;"></i>
                                    <p class="mb-1" style="font-size: 16px; font-weight: 500; color: #475569;">Chưa có đánh giá nào cho món ăn này.</p>
                                    <p class="text-muted small">Hãy đặt mua món ăn để trở thành người đầu tiên viết đánh giá!</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- <!-- Related Foods -->
            @if($relatedFoods->count() > 0)
            <div class="related-foods-section mt-5">
                <h3 class="mb-4">Món ăn liên quan</h3>
                <div class="row">
                    @foreach($relatedFoods as $relatedFood)
                    <div class="col-sm-6 col-lg-3 mb-4">
                        <div class="box h-100">
                            <div class="img-box">
                                <img src="{{ $relatedFood->HinhAnh ? asset('images/' . $relatedFood->HinhAnh) : asset('images/no-image.png') }}"
                                    alt="{{ $relatedFood->TenMonAn }}">
                            </div>
                            <div class="detail-box">
                                <h6>
                                    <a href="{{ route('food.detail', $relatedFood->MaMonAn) }}"
                                        class="text-decoration-none">
                                        {{ $relatedFood->TenMonAn }}
                                    </a>
                                </h6>
                                <div class="options">
                                    <h6>{{ number_format($relatedFood->Gia, 0, ',', '.') }} đ</h6>
                                    <button type="button"
                                        class="btn btn-warning rounded-circle d-flex align-items-center justify-content-center add-to-cart-btn"
                                        style="width: 40px; height: 40px;" data-id="{{ $relatedFood->MaMonAn }}"
                                        data-name="{{ $relatedFood->TenMonAn }}" data-price="{{ $relatedFood->Gia }}"
                                        data-image="{{ $relatedFood->HinhAnh ? asset('images/' . $relatedFood->HinhAnh) : asset('images/no-image.png') }}">
                                        <i class="fa fa-shopping-cart"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </section> --}}

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

        .food_detail_section {
            min-height: 80vh;
            background: #f8fafc !important; /* Soft, clean premium off-white */
            color: #1e293b !important;
            padding: 50px 0 80px 0 !important;
        }

        .breadcrumb {
            background-color: transparent !important;
            padding: 0;
        }

        .breadcrumb-item a {
            color: #ff9f00 !important;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .breadcrumb-item a:hover {
            color: #ff6f00 !important;
        }

        .breadcrumb-item.active {
            color: #64748b !important;
        }

        .breadcrumb-item + .breadcrumb-item::before {
            color: #94a3b8 !important;
        }

        .food-image-container {
            background: #ffffff;
            border: 1px solid rgba(0, 0, 0, 0.05);
            border-radius: 20px;
            padding: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03);
            transition: all 0.3s ease;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .food-image-container:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.07);
            border-color: rgba(255, 190, 51, 0.4);
        }

        .food-image-container img {
            width: 100%;
            height: 380px !important;
            object-fit: cover !important;
            border-radius: 12px !important;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.06) !important;
        }

        .food-info {
            background: #ffffff;
            border: 1px solid rgba(0, 0, 0, 0.05);
            border-radius: 20px;
            padding: 35px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03);
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .food-title {
            color: #1e293b !important;
            font-weight: 700;
            font-size: 32px;
            margin-bottom: 12px;
            letter-spacing: -0.5px;
        }

        .rating-overview .stars i {
            font-size: 16px;
        }

        .rating-text {
            color: #64748b !important;
            font-size: 14px;
            font-weight: 500;
        }

        .restaurant-avatar,
        .reviewer-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #f7931e;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            color: white;
        }

        .reviewer-avatar {
            width: 35px;
            height: 35px;
            font-size: 14px;
        }

        /* Star Rating Form */
        .star-rating {
            direction: rtl;
            display: inline-block;
        }

        .star-rating input[type=radio] {
            display: none;
        }

        .star-rating label {
            color: #ddd;
            font-size: 30px;
            padding: 0 5px;
            cursor: pointer;
            transition: color 0.2s;
        }

        .star-rating input[type=radio]:checked~label,
        .star-rating label:hover,
        .star-rating label:hover~label {
            color: #ffb400;
        }

        .review-item {
            background: #f8f9fa;
            border: 1px solid #e9ecef !important;
        }

        .review-stars i {
            font-size: 14px;
        }

        .add-to-cart-btn {
            transition: all 0.3s ease;
        }

        .add-to-cart-btn:hover {
            transform: scale(1.05);
            background: #ff6b35 !important;
        }

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



        /* Horizontal Reviews Carousel style */
        .reviews-section {
            background: #ffffff !important;
            border-radius: 20px;
            padding: 30px 25px;
            border: 1px solid rgba(0, 0, 0, 0.05) !important;
            margin-top: 50px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03) !important;
        }
        .reviews-title {
            font-family: 'Arimo', sans-serif;
            font-weight: 700;
            color: #1e293b !important;
            font-size: 22px;
            border-bottom: 2px solid #ffbe33;
            display: inline-block;
            padding-bottom: 8px;
        }
        .reviews-horizontal-container {
            display: flex;
            gap: 20px;
            overflow-x: auto;
            padding: 15px 5px 15px 5px;
            scroll-snap-type: x mandatory;
            scrollbar-width: none; /* Hide scrollbar Firefox */
            -ms-overflow-style: none; /* Hide scrollbar IE/Edge */
        }
        
        /* Hide scrollbar for Chrome, Safari and Opera */
        .reviews-horizontal-container::-webkit-scrollbar {
            display: none;
        }

        /* Center single comment properly */
        .reviews-horizontal-container.single-review-mode {
            justify-content: center;
            overflow-x: hidden;
            padding: 15px 5px;
        }
        
        .reviews-horizontal-container.single-review-mode .review-card-item {
            flex: 0 1 600px;
            width: 100%;
        }
        
        .review-card-item {
            flex: 0 0 380px; /* Multiple cards side-by-side on PC */
            background: #f8fafc !important;
            border: 1px solid rgba(0, 0, 0, 0.05) !important;
            border-radius: 16px;
            padding: 20px;
            scroll-snap-align: start;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.02) !important;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            box-sizing: border-box;
        }

        /* Highlight pulse effect for review comment from notification click */
        @keyframes reviewGlowPulse {
            0% {
                box-shadow: 0 0 0 0 rgba(255, 190, 51, 0.9);
                border-color: #ffbe33 !important;
                background: #fff9db !important;
                transform: translateY(-6px) scale(1.03);
            }
            30% {
                box-shadow: 0 0 25px 12px rgba(255, 190, 51, 0.5);
                border-color: #ffbe33 !important;
                background: #fffbeb !important;
                transform: translateY(-6px) scale(1.03);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(255, 190, 51, 0);
                border-color: rgba(0, 0, 0, 0.05) !important;
                background: #f8fafc !important;
                transform: translateY(0) scale(1);
            }
        }
        .review-card-item.highlight-pulse {
            animation: reviewGlowPulse 4s ease-out forwards;
            position: relative;
            z-index: 10;
        }
        .review-card-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.06) !important;
            border-color: rgba(255, 190, 51, 0.4) !important;
            background: #ffffff !important;
        }
        .reviewer-name {
            font-size: 15px;
            color: #1e293b !important;
            font-weight: 600;
        }
        .review-time {
            font-size: 11px;
            color: #64748b !important;
        }
        .review-text {
            font-size: 14px;
            color: #475569 !important;
            line-height: 1.6;
            word-break: break-word;
            margin-top: 10px;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        /* Removed hover expansion to maintain fixed card height */
        .seller-reply {
            background-color: #fff9f0 !important;
            border-left: 3px solid #ffbe33;
            margin-top: 12px;
            padding: 10px 12px !important;
            border-radius: 8px;
            color: #475569 !important;
        }

        @media (max-width: 768px) {
            .review-card-item {
                flex: 0 0 100%;
                scroll-snap-align: center;
            }
        }
        #detailBackButton:hover {
            background-color: #e0a800 !important;
            transform: translateX(-4px);
            box-shadow: 0 4px 10px rgba(255, 190, 51, 0.3) !important;
        }
    </style>

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

            // Handle Detail Back Button redirection logic
            const backBtn = document.getElementById('detailBackButton');
            if (backBtn) {
                const urlParams = new URLSearchParams(window.location.search);
                const fromRestaurant = urlParams.get('from_restaurant');
                if (fromRestaurant) {
                    // Go back to homepage and auto-trigger restaurant modal open
                    backBtn.setAttribute('href', `/?open_restaurant=${fromRestaurant}`);
                } else {
                    // Go back to standard homepage
                    backBtn.setAttribute('href', '/');
                }
            }
        });
    </script>

    <!-- Load food detail from RESTful API -->
    <script src="{{ asset('js/api-food-detail.js') }}"></script>

    {{-- ===== REVIEWS CAROUSEL CONTROLLER ===== --}}
    <script>
        (function () {
            const container = document.getElementById('reviewsContainer');
            if (!container) return;

            const cards = container.querySelectorAll('.review-card-item');
            if (cards.length === 0) return;

            const prevBtn = document.getElementById('reviewPrev');
            const nextBtn = document.getElementById('reviewNext');
            let currentIndex = 0;
            const total = cards.length;

            window.reviewsNav = function (dir) {
                if (total <= 1) return;

                if (window.innerWidth <= 768) {
                    // Mobile: slide exactly one card (index-by-index)
                    currentIndex = currentIndex + dir;
                    if (currentIndex < 0) {
                        currentIndex = 0;
                    } else if (currentIndex >= total) {
                        currentIndex = total - 1;
                    }
                    scrollToIndex(currentIndex);
                } else {
                    // PC: slide by one card width (380px) + gap (20px) = 400px
                    container.scrollBy({
                        left: dir * 400,
                        behavior: 'smooth'
                    });
                }
            };

            function scrollToIndex(idx) {
                const targetCard = cards[idx];
                if (targetCard) {
                    container.scrollTo({
                        left: targetCard.offsetLeft - container.offsetLeft,
                        behavior: 'smooth'
                    });
                }
                updateButtons();
            }

            function updateButtons() {
                const canScroll = container.scrollWidth > container.clientWidth + 5;
                if (prevBtn && nextBtn) {
                    if (!canScroll) {
                        prevBtn.style.display = 'none';
                        nextBtn.style.display = 'none';
                        return;
                    } else {
                        prevBtn.style.display = 'flex';
                        nextBtn.style.display = 'flex';
                    }
                }

                const atStart = container.scrollLeft <= 10;
                const atEnd   = container.scrollLeft + container.clientWidth >= container.scrollWidth - 10;
                if (prevBtn) {
                    prevBtn.style.opacity = atStart ? '0.2' : '1';
                    prevBtn.style.pointerEvents = atStart ? 'none' : 'auto';
                }
                if (nextBtn) {
                    nextBtn.style.opacity = atEnd ? '0.2' : '1';
                    nextBtn.style.pointerEvents = atEnd ? 'none' : 'auto';
                }
            }

            window.addEventListener('resize', updateButtons);

            // Sync index on manual swipe/scroll
            let scrollTimeout;
            container.addEventListener('scroll', () => {
                updateButtons();

                if (window.innerWidth <= 768) {
                    clearTimeout(scrollTimeout);
                    scrollTimeout = setTimeout(() => {
                        const width = container.offsetWidth;
                        if (width > 0) {
                            const newIndex = Math.round(container.scrollLeft / width);
                            if (newIndex >= 0 && newIndex < total && newIndex !== currentIndex) {
                                currentIndex = newIndex;
                            }
                        }
                    }, 100);
                }
            });

            // Check for direct review target via URL hash
            function handleHashHighlight() {
                const hash = window.location.hash;
                if (hash && hash.startsWith('#review-')) {
                    const targetCard = container.querySelector(hash);
                    if (targetCard) {
                        // Reset animation if already present
                        targetCard.classList.remove('highlight-pulse');
                        void targetCard.offsetWidth; // Trigger DOM reflow to restart CSS keyframe animation
                        targetCard.classList.add('highlight-pulse');
                        
                        // Auto-scroll the horizontal carousel container to this card
                        setTimeout(() => {
                            container.scrollTo({
                                left: targetCard.offsetLeft - container.offsetLeft - 20,
                                behavior: 'smooth'
                            });
                            updateButtons();
                        }, 300);
                    }
                }
            }

            // Bind to window load and hashchange events for maximum reliability
            if (document.readyState === 'complete') {
                handleHashHighlight();
            } else {
                window.addEventListener('load', handleHashHighlight);
            }
            window.addEventListener('hashchange', handleHashHighlight);

            updateButtons(); // Init state

            // Hover effects
            [prevBtn, nextBtn].forEach(btn => {
                if (!btn) return;
                btn.addEventListener('mouseenter', () => {
                    btn.style.background = '#ffbe33';
                    btn.style.color      = '#1e293b';
                    btn.style.transform  = 'translateY(-50%) scale(1.12)';
                });
                btn.addEventListener('mouseleave', () => {
                    btn.style.background = '#ffffff';
                    btn.style.color      = '#64748b';
                    btn.style.transform  = 'translateY(-50%) scale(1)';
                });
            });

            // Touch swipe on mobile
            let touchStartX = 0;
            container.addEventListener('touchstart', e => { touchStartX = e.touches[0].clientX; });
            container.addEventListener('touchend', e => {
                const diff = touchStartX - e.changedTouches[0].clientX;
                if (Math.abs(diff) > 40) reviewsNav(diff > 0 ? 1 : -1);
            });
        })();
    </script>

    {{-- ===== MEDIA GALLERY CONTROLLER ===== --}}
    <script>
        (function () {
            const slides  = document.querySelectorAll('.gallery-slide');
            const thumbs  = document.querySelectorAll('.gallery-thumb');
            const counter = document.getElementById('galleryCounter');
            let current   = 0;
            const total   = slides.length;

            if (total <= 1) return; // Nothing to navigate

            function galleryGoTo(idx) {
                // Hide current slide
                slides[current].style.display = 'none';
                if (thumbs && thumbs.length > current) {
                    thumbs[current].style.border  = '2.5px solid transparent';
                    thumbs[current].style.opacity  = '0.55';
                }

                // Show new slide
                current = (idx + total) % total;
                slides[current].style.display = 'flex';
                if (thumbs && thumbs.length > current) {
                    thumbs[current].style.border  = '2.5px solid #ffbe33';
                    thumbs[current].style.opacity  = '1';
                    thumbs[current].scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
                }

                // Update counter
                if (counter) counter.textContent = (current + 1) + ' / ' + total;
            }

            // Expose to global scope for onclick
            window.galleryGoTo = galleryGoTo;
            window.galleryNav  = function (dir) { galleryGoTo(current + dir); };

            // Hover effect on nav buttons
            document.querySelectorAll('.gallery-nav-btn').forEach(btn => {
                btn.addEventListener('mouseenter', () => {
                    btn.style.background   = '#ffbe33';
                    btn.style.color        = '#1e293b';
                    btn.style.transform    = 'translateY(-50%) scale(1.1)';
                });
                btn.addEventListener('mouseleave', () => {
                    btn.style.background   = 'rgba(255,255,255,0.9)';
                    btn.style.color        = '#1e293b';
                    btn.style.transform    = 'translateY(-50%) scale(1)';
                });
            });

            // Swipe/touch support
            let touchStartX = 0;
            const track = document.getElementById('galleryTrack');
            if (track) {
                track.addEventListener('touchstart', e => { touchStartX = e.touches[0].clientX; });
                track.addEventListener('touchend', e => {
                    const diff = touchStartX - e.changedTouches[0].clientX;
                    if (Math.abs(diff) > 40) galleryNav(diff > 0 ? 1 : -1);
                });
            }
        })();

        // Single Comment Gallery Handler
        let currentLightboxMedia = [];
        let currentLightboxIndex = 0;

        function openCommentGallery(element, startIndex = 0) {
            try {
                let mediaList = JSON.parse(element.getAttribute('data-media'));
                if (!Array.isArray(mediaList)) mediaList = [mediaList];
                currentLightboxMedia = mediaList;
                currentLightboxIndex = startIndex;
                
                renderLightboxContent();
                
                // Open modal based on Bootstrap version available
                if (typeof bootstrap !== 'undefined') {
                    var myModal = new bootstrap.Modal(document.getElementById('commentGalleryModal'));
                    myModal.show();
                } else if (typeof $ !== 'undefined') {
                    $('#commentGalleryModal').modal('show');
                }
            } catch (e) {
                console.error("Error parsing media data", e);
            }
        }

        function renderLightboxContent() {
            if(currentLightboxMedia.length === 0) return;
            
            let path = currentLightboxMedia[currentLightboxIndex];
            let ext = path.split('.').pop().toLowerCase();
            let isVideo = ['mp4', 'mov', 'avi'].includes(ext);
            let url = (path.startsWith('http://') || path.startsWith('https://')) ? path : '{{ asset("images/") }}/' + path;
            
            let contentHtml = '';
            if (isVideo) {
                contentHtml = `<video src="${url}" controls autoplay style="max-width: 90vw; max-height: 80vh; border-radius: 8px; box-shadow: 0 5px 25px rgba(0,0,0,0.5); outline: none;"></video>`;
            } else {
                contentHtml = `<img src="${url}" style="max-width: 90vw; max-height: 80vh; border-radius: 8px; box-shadow: 0 5px 25px rgba(0,0,0,0.5); object-fit: contain;">`;
            }
            
            let counterHtml = '';
            let navHtml = '';
            
            if(currentLightboxMedia.length > 1) {
                counterHtml = `<div style="color: #94a3b8; font-size: 15px; font-weight: 500; text-shadow: 0 2px 4px rgba(0,0,0,0.5); margin-top: 20px; letter-spacing: 2px;">
                                ${currentLightboxIndex + 1} / ${currentLightboxMedia.length}
                               </div>`;
                navHtml = `
                    <button onclick="navigateLightbox(-1)" style="position: absolute; left: 3vw; top: 50%; transform: translateY(-50%); background: rgba(255,255,255,0.05); border: none; color: white; width: 60px; height: 60px; border-radius: 50%; font-size: 24px; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s; backdrop-filter: blur(4px);" onmouseover="this.style.background='rgba(255,255,255,0.15)'" onmouseout="this.style.background='rgba(255,255,255,0.05)'">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <button onclick="navigateLightbox(1)" style="position: absolute; right: 3vw; top: 50%; transform: translateY(-50%); background: rgba(255,255,255,0.05); border: none; color: white; width: 60px; height: 60px; border-radius: 50%; font-size: 24px; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s; backdrop-filter: blur(4px);" onmouseover="this.style.background='rgba(255,255,255,0.15)'" onmouseout="this.style.background='rgba(255,255,255,0.05)'">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                `;
            }
            
            let html = `
                <div style="position: relative; width: 100%; height: 100vh; display: flex; flex-direction: column; align-items: center; justify-content: center;">
                    ${contentHtml}
                    ${counterHtml}
                    ${navHtml}
                </div>
            `;
            
            document.getElementById('commentGalleryBody').innerHTML = html;
        }

        function navigateLightbox(dir) {
            currentLightboxIndex += dir;
            if(currentLightboxIndex < 0) currentLightboxIndex = currentLightboxMedia.length - 1;
            if(currentLightboxIndex >= currentLightboxMedia.length) currentLightboxIndex = 0;
            renderLightboxContent();
        }
    </script>

    <!-- Single Comment Gallery Modal -->
    <div class="modal fade" id="commentGalleryModal" tabindex="-1" aria-hidden="true" style="z-index: 1080;">
        <div class="modal-dialog" style="max-width: 100%; margin: 0; height: 100vh;">
            <div class="modal-content" style="background: rgba(10,10,10,0.98); border: none; box-shadow: none; height: 100%; border-radius: 0;">
                <button type="button" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close" 
                        style="position: absolute; top: 25px; right: 35px; z-index: 1060; background: transparent; border: none; color: white; font-size: 28px; opacity: 0.6; cursor: pointer; padding: 10px; transition: opacity 0.2s;" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.6'">
                    <i class="fas fa-times"></i>
                </button>
                <div class="modal-body p-0" id="commentGalleryBody" style="height: 100%; overflow: hidden;">
                    <!-- Images/Videos injected via JS -->
                </div>
            </div>
        </div>
    </div>

    <!-- All Reviews Modal -->
    <div class="modal fade" id="allReviewsModal" tabindex="-1" aria-labelledby="allReviewsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
            <div class="modal-content" style="border-radius: 20px; border: none; overflow: hidden; box-shadow: 0 10px 40px rgba(0,0,0,0.1);">
                <div class="modal-header" style="background: #f8fafc; border-bottom: 1px solid rgba(0,0,0,0.05); padding: 20px 25px;">
                    <h5 class="modal-title" id="allReviewsModalLabel" style="font-weight: 700; color: #1e293b; font-size: 20px;">
                        Tất cả đánh giá ({{ $food->binhLuans->count() }})
                    </h5>
                    <button type="button" class="close btn-close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close" style="background: transparent; border: none; font-size: 24px;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="padding: 25px; background: #ffffff;">
                    @if($food->binhLuans->count() > 0)
                        <div class="vertical-reviews-list d-flex flex-column" style="gap: 20px;">
                            @foreach($food->binhLuans as $binhLuan)
                                <div class="review-item-vertical p-4" style="border: 1px solid rgba(0,0,0,0.05); border-radius: 16px; background: #f8fafc; transition: all 0.2s ease;">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div class="reviewer-info d-flex align-items-center">
                                            <div class="reviewer-avatar mr-3 me-3" style="flex-shrink: 0; width: 45px; height: 45px; border-radius: 50%; background: #f7931e; color: white; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 18px;">
                                                {{ substr($binhLuan->nguoiDung->HoTen, 0, 1) }}
                                            </div>
                                            <div>
                                                <strong style="color: #1e293b; font-size: 16px; display: block; margin-bottom: 2px;">{{ $binhLuan->nguoiDung->HoTen }}</strong>
                                                <div class="review-stars">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        @if($i <= $binhLuan->diem_danh_gia)
                                                            <i class="fa fa-star text-warning" style="font-size: 13px;"></i>
                                                        @else
                                                            <i class="fa fa-star text-muted" style="font-size: 13px;"></i>
                                                        @endif
                                                    @endfor
                                                </div>
                                            </div>
                                        </div>
                                        <small class="text-muted" style="font-size: 13px;">{{ $binhLuan->created_at->diffForHumans() }}</small>
                                    </div>
                                    
                                    <div class="review-body">
                                        <p style="color: #334155; font-size: 15px; line-height: 1.6; margin-bottom: 15px; word-break: break-word;">
                                            {{ $binhLuan->noi_dung }}
                                        </p>
                                        
                                        @if($binhLuan->hinh_anh)
                                            @php
                                                $decoded = json_decode($binhLuan->hinh_anh, true);
                                                $mediaList = is_array($decoded) ? $decoded : [$binhLuan->hinh_anh];
                                            @endphp
                                            <div class="d-flex flex-wrap" style="gap: 10px;">
                                                @foreach($mediaList as $idx => $mediaPath)
                                                    @php
                                                        $ext = strtolower(pathinfo($mediaPath, PATHINFO_EXTENSION));
                                                        $isVideo = in_array($ext, ['mp4', 'mov', 'avi']);
                                                    @endphp
                                                    <div class="review-media-item" style="width: 110px; height: 110px; border-radius: 10px; overflow: hidden; border: 1px solid #e2e8f0; box-shadow: 0 2px 4px rgba(0,0,0,0.02); background: #ffffff; cursor: pointer; position: relative;"
                                                         data-media="{{ json_encode($mediaList) }}" onclick="openCommentGallery(this, {{ $idx }})">
                                                        @php
                                                            $mediaUrl = filter_var($mediaPath, FILTER_VALIDATE_URL) ? $mediaPath : asset('images/' . $mediaPath);
                                                        @endphp
                                                        @if($isVideo)
                                                            <video src="{{ $mediaUrl }}" style="width: 100%; height: 100%; object-fit: cover;"></video>
                                                        @else
                                                            <img src="{{ $mediaUrl }}" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.2s ease;" onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'">
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif

                                        @if($binhLuan->phan_hoi)
                                            <div class="seller-reply mt-3 p-3 rounded" style="background-color: #fff9f0; border-left: 4px solid #ffbe33;">
                                                <div class="d-flex align-items-center mb-1">
                                                    <span class="fw-bold text-warning" style="font-size: 14px;"><i class="fas fa-store me-1"></i> Chủ quán phản hồi:</span>
                                                </div>
                                                <div class="text-dark" style="font-style: italic; font-size: 14px; line-height: 1.6;">"{{ $binhLuan->phan_hoi }}"</div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <p class="text-muted">Chưa có đánh giá nào.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

@endsection