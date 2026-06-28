@extends('seller.layouts.app')
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
                <form id="foodForm" action="{{ route('api.seller.foods.update', $food->MaMonAn) }}" method="POST"
                    enctype="multipart/form-data" data-mode="edit" data-ajax="1" data-original-name="{{ $food->TenMonAn }}"
                    data-food-id="{{ $food->MaMonAn }}" data-index-url="{{ route('seller.foods.index') }}"
                    data-check-name-url="{{ route('api.seller.foods.check-name') }}">
                    @csrf
                    @method('PUT')
                    @include('seller.foods._form', ['food' => $food])
                    <div class="mt-3">
                        <button class="btn btn-primary" type="submit"><i class="fas fa-save"></i> Cập nhật</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
(function() {
    "use strict";

    function init() {
        const form = document.getElementById("foodForm");
        if (!form) return;

        form.addEventListener("submit", async function(e) {
            e.preventDefault();
            e.stopPropagation();

            // Clear old alerts
            const oldAlert = form.querySelector('.alert-floating');
            if (oldAlert) oldAlert.remove();

            const submitBtn = form.querySelector('button[type="submit"]');
            const originalBtnHtml = submitBtn.innerHTML;
            
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang xử lý...';

            try {
                const formData = new FormData(form);
                const response = await fetch(form.action, {
                    method: 'POST', // Laravel uses POST + _method for PUT
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const result = await response.json();

                if (response.ok) {
                    const alert = document.createElement('div');
                    alert.className = 'alert alert-success alert-floating mt-3';
                    alert.innerHTML = '<i class="fas fa-check-circle"></i> ' + (result.message || 'Thành công!');
                    form.prepend(alert);
                    alert.scrollIntoView({ behavior: 'smooth', block: 'center' });

                    setTimeout(() => {
                        window.location.href = form.dataset.indexUrl || '/seller/foods';
                    }, 1000);
                } else {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnHtml;
                    
                    let errorMsg = result.message || 'Có lỗi xảy ra';
                    if (result.errors) {
                        errorMsg = Object.values(result.errors).flat().join('<br>');
                    }
                    
                    const alert = document.createElement('div');
                    alert.className = 'alert alert-danger alert-floating mt-3';
                    alert.innerHTML = '<i class="fas fa-exclamation-circle"></i> ' + errorMsg;
                    form.prepend(alert);
                    alert.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            } catch (error) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnHtml;
                alert('Lỗi kết nối hệ thống. Vui lòng thử lại.');
            }
        });
    }

    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", init);
    } else {
        init();
    }
})();
</script>
@endpush