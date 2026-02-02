@php
    $editing = isset($food);
@endphp

@if(isset($maNhaHang))
    <div class="alert alert-light border d-flex align-items-center py-2">
        <i class="fas fa-store text-primary me-2"></i>
        <div>
            <strong>Nhà hàng của bạn:</strong>
            <span class="text-muted">#{{ $maNhaHang }}</span>
        </div>
    </div>
@endif

<div class="row">
    {{-- <div class="col-md-4">
        <label class="form-label">Mã nhà hàng</label>
        <input type="number" name="MaNhaHang" class="form-control @error('MaNhaHang') is-invalid @enderror"
            value="{{ old('MaNhaHang', $editing ? $food->MaNhaHang : '') }}" required>
        @error('MaNhaHang') <div class="invalid-feedback">{{ $message }}</div> @enderror
        <small class="text-muted">Nhập mã nhà hàng thuộc quyền của bạn.</small>
    </div> --}}
    <div class="col-md-12">
        <label class="form-label">Tên món ăn</label>
        <input type="text" name="TenMonAn" class="form-control "
            value="{{ old('TenMonAn', $editing ? $food->TenMonAn : '') }}" maxlength="150">
    </div>
</div>

<div class="row mt-2">
    <div class="col-md-4">
        <label class="form-label">Danh mục</label>
        <input type="text" name="DanhMuc" class="form-control" list="danhMucOptions"
            value="{{ old('DanhMuc', $editing ? $food->DanhMuc : '') }}" placeholder="Nhập hoặc chọn danh mục">
        <datalist id="danhMucOptions">
            @foreach($danhMuc as $opt)
                <option value="{{ $opt }}">{{ $opt }}</option>
            @endforeach
        </datalist>
    </div>
    <div class="col-md-4">
        <label class="form-label">Giá (đ)</label>
        <input type="text" name="Gia" step="1000" class="form-control "
            value="{{ old('Gia', $editing ? (float) $food->Gia : '') }}">

    </div>
    <div class="col-md-4">
        <label class="form-label">Trạng thái</label>
        <select name="TrangThai" class="form-select ">
            @foreach($trangThai as $opt)
                <option value="{{ $opt }}" {{ old('TrangThai', $editing ? $food->TrangThai : 'Còn bán') === $opt ? 'selected' : '' }}>
                    {{ $opt }}
                </option>
            @endforeach
        </select>
    </div>
</div>

<div class="mt-2">
    <label class="form-label">Mô tả</label>
    <textarea name="MoTa" rows="2" class="form-control "
        maxlength="255">{{ old('MoTa', $editing ? $food->MoTa : '') }}</textarea>
</div>

<div class="mt-2">
    <label class="form-label">Hình ảnh</label>
    <input type="file" name="HinhAnh" accept=".jpg,.jpeg,.png,.webp" class="form-control ">

    @if($editing && $food->HinhAnh)
        <div class="mt-2">
            <img src="{{ asset('images/' . $food->HinhAnh) }}" alt="preview"
                style="width:120px;height:90px;object-fit:cover;border-radius:8px;">
            <div class="text-muted small mt-1">Tên file đang lưu: {{ $food->HinhAnh }}</div>
        </div>
    @endif
    <small class="text-muted d-block mt-1">
        Định dạng: JPG/JPEG/PNG. Tối đa 5MB (2E.12, 2E.13).
        @if(!$editing)
            Bắt buộc chọn ảnh khi thêm mới (2E.11).
        @endif
    </small>
</div>