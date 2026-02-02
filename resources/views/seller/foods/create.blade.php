@extends('seller.layout')
@section('title', 'Thêm món ăn')
@section('content')
    <div class="container-fluid">
        <div class="card shadow">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Thêm món ăn</h6>
                <a href="{{ route('seller.foods.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Quay lại
                </a>
            </div>
            <div class="card-body">
                <form id="foodForm" action="{{ route('seller.foods.store') }}" method="POST" enctype="multipart/form-data"
                    data-mode="create" data-ajax="1">
                    @csrf
                    @include('seller.foods._form')
                    <div class="mt-3">
                        <button class="btn btn-primary" type="submit"><i class="fas fa-save"></i> Lưu</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/seller-foods-validator.js') }}"></script>
@endpush