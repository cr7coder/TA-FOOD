@extends('seller.layout')
@section('title', 'Sửa món ăn')
@section('content')
    <div class="container-fluid">
        <div class="card shadow">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Sửa món: {{ $food->TenMonAn }}</h6>
                <a href="{{ route('seller.foods.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Quay lại
                </a>
            </div>
            <div class="card-body">
                <form id="foodForm" action="{{ route('seller.foods.update', $food->MaMonAn) }}" method="POST"
                    enctype="multipart/form-data" data-mode="edit" data-ajax="1" data-original-name="{{ $food->TenMonAn }}"
                    data-food-id="{{ $food->MaMonAn }}" data-index-url="{{ route('seller.foods.index') }}">
                    @csrf
                    @method('PUT')
                    @include('seller.foods._form', ['food' => $food, 'maNhaHang' => $maNhaHang])
                    <div class="mt-3">
                        <button class="btn btn-primary" type="submit"><i class="fas fa-save"></i> Cập nhật</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/seller-foods-validator.js') }}"></script>
@endpush