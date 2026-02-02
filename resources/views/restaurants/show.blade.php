@extends('foods.master')

@section('content')
    <div class="container mt-5">
        <div class="heading_container heading_center mb-4 ">
            <h2>Thực đơn của {{ $restaurant->TenNhaHang }}</h2>
            <a href="{{ route('foods.index') }}" class="btn btn-warning btn-lg rounded-pill shadow"
                style="margin-top: 20px;">
                ← Quay lại danh sách nhà hàng
            </a>
        </div>
    </div>

    {{-- Giữ nguyên “food section” để CSS áp dụng giống hệt --}}
    <section class="food_section layout_padding-bottom">
        <div class="container">
            {{-- (Nếu không dùng filter menu thì bỏ, chỉ giữ cấu trúc) --}}
            <div class="filters-content">
                <div class="row grid">
                    @forelse($foods as $food)
                        <div
                            class="col-sm-6 col-lg-4 all {{ $food->loai_mon_class }} {{ $food->TrangThai == 'Còn bán' ? 'con-ban' : 'ngung-ban' }}">
                            <div class="box">
                                <div>
                                    <div class="img-box">
                                        <img src="{{ $food->HinhAnh ? asset('images/' . $food->HinhAnh) : asset('images/no-image.png') }}"
                                            alt="{{ $food->TenMonAn }}">
                                    </div>
                                    <div class="detail-box">
                                        <h5>{{ $food->TenMonAn }}</h5>
                                        @if($food->MoTa)
                                            <p>{{ \Illuminate\Support\Str::limit($food->MoTa, 40, '...') }}</p>
                                        @endif
                                        <div class="options">
                                            <h6>{{ number_format($food->Gia, 0, ',', '.') }} đ</h6>
                                            <form action="{{ route('giohang.add', $food->MaMonAn) }}" method="POST">
                                                @csrf
                                                <button type="submit"
                                                    class="btn btn-warning rounded-circle d-flex align-items-center justify-content-center"
                                                    style="width:50px;height:50px;">
                                                    <i class="fa fa-shopping-cart fa-lg"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <p class="text-center text-muted">Nhà hàng chưa có món nào.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </section>
@endsection