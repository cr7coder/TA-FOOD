@php
    $editing = isset($food);
    $currentMaNhaHang = $maNhaHang ?? ($editing ? $food->MaNhaHang : null);
@endphp

@if($currentMaNhaHang)
    <div class="alert alert-light border d-flex align-items-center py-2">
        <i class="fas fa-store text-primary me-2"></i>
        <div>
            <strong>Nhà hàng của bạn:</strong>
            <span class="text-muted">#{{ $currentMaNhaHang }}</span>
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
        <select name="DanhMuc" class="form-select">
            <option value="">-- Chọn danh mục --</option>
            @foreach($danhMuc as $opt)
                <option value="{{ $opt }}" {{ old('DanhMuc', $editing ? $food->DanhMuc : '') === $opt ? 'selected' : '' }}>
                    {{ $opt }}
                </option>
            @endforeach
        </select>
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
    <label class="form-label">Hình ảnh minh họa <span class="text-danger">*</span></label>
    <div class="image-upload" id="imageUpload">
        <div class="image-upload-icon">
            <i class="fas fa-cloud-upload-alt"></i>
        </div>
        <div class="image-upload-text">Thêm hình ảnh món ăn</div>
        <div class="image-upload-hint">Định dạng JPG, PNG, WEBP tối đa 5MB (tối đa 5 tệp)</div>
        <input type="file" 
               class="file-input" 
               id="hinhAnh" 
               name="images[]" 
               multiple
               accept="image/jpeg,image/jpg,image/png,image/webp">
    </div>
    <div class="file-selected" id="fileSelected" style="display: none;">Không có tệp nào được chọn</div>
    <div class="invalid-feedback" id="images-error"></div>
    <div class="image-preview" id="imagePreview">
        @if($editing && $food->HinhAnh)
            <div class="image-preview-item" style="position: relative;">
                <img src="{{ $food->hinh_anh_url }}" class="image-preview-img" alt="preview">
            </div>
            @foreach($food->thu_vien_anh_urls as $imgUrl)
                <div class="image-preview-item" style="position: relative;">
                    <img src="{{ $imgUrl }}" class="image-preview-img" alt="preview">
                </div>
            @endforeach
        @endif
    </div>
    
    <small class="text-muted d-block mt-2">
        <i class="fas fa-info-circle"></i> Ảnh đầu tiên sẽ là ảnh chính của món ăn.
        @if(!$editing)
            Bắt buộc chọn ít nhất 1 ảnh khi thêm mới.
        @else
            Chọn ảnh mới sẽ ghi đè lên toàn bộ ảnh cũ.
        @endif
    </small>
</div>

<style>
    .image-upload { border: 2px dashed #cbd5e1; border-radius: 16px; padding: 30px; text-align: center; cursor: pointer; background: #f8fafc; transition: all 0.25s ease; }
    .image-upload:hover { border-color: #4e73df; background: rgba(78, 115, 223, 0.02); }
    .image-upload-icon { font-size: 32px; color: #4e73df; margin-bottom: 8px; transition: transform 0.25s ease; }
    .image-upload:hover .image-upload-icon { transform: translateY(-4px); }
    .image-upload-text { font-family: inherit; font-size: 14px; font-weight: 700; color: #334155; margin-bottom: 4px; }
    .image-upload-hint { font-family: inherit; font-size: 12px; color: #64748b; }
    .file-input { display: none; }
    .file-selected { font-family: inherit; font-size: 12.5px; font-weight: 600; color: #4e73df; margin-top: 8px; }
    .image-preview { margin-top: 16px; display: flex; gap: 12px; flex-wrap: wrap; }
    .image-preview-item { position: relative; width: 100px; height: 100px; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.08); border: 1px solid #cbd5e1; }
    .image-preview-img { width: 100%; height: 100%; object-fit: cover; }
    .image-preview-remove { position: absolute; top: 6px; right: 6px; background: rgba(15, 23, 42, 0.7); border: none; border-radius: 50%; width: 22px; height: 22px; color: white; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s; }
    .image-preview-remove:hover { background: #ef4444; transform: scale(1.1); }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const imageUpload = document.getElementById('imageUpload');
    const fileInput = document.getElementById('hinhAnh');
    const fileSelected = document.getElementById('fileSelected');
    const imagePreview = document.getElementById('imagePreview');
    let accumulatedFiles = new DataTransfer();
    
    if (imageUpload && fileInput) {
        imageUpload.addEventListener('click', function() {
            fileInput.click();
        });
        
        fileInput.addEventListener('change', function() {
            if (this.files && this.files.length > 0) {
                let overflow = false;
                Array.from(this.files).forEach(file => {
                    if (accumulatedFiles.items.length < 5) {
                        let exists = false;
                        for (let i = 0; i < accumulatedFiles.items.length; i++) {
                            if (accumulatedFiles.files[i].name === file.name && accumulatedFiles.files[i].size === file.size) {
                                exists = true; break;
                            }
                        }
                        if (!exists) {
                            accumulatedFiles.items.add(file);
                        }
                    } else {
                        overflow = true;
                    }
                });
                
                if (overflow) {
                    alert('Chỉ được tải lên tối đa 5 tệp. Các tệp chọn thêm đã bị bỏ qua.');
                }
            }
            
            this.files = accumulatedFiles.files;
            renderPreviews();
        });

        function renderPreviews() {
            if (accumulatedFiles.items.length > 0) {
                fileSelected.textContent = `Đã chọn: ${accumulatedFiles.items.length} tệp`;
                fileSelected.style.display = 'block';
                imagePreview.innerHTML = '';
                
                Array.from(accumulatedFiles.files).forEach((file, index) => {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        imagePreview.innerHTML += `
                            <div class="image-preview-item" style="position: relative;">
                                <img src="${e.target.result}" class="image-preview-img" alt="Preview">
                                <button type="button" class="image-preview-remove" onclick="removeSpecificFile(${index})">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        `;
                    };
                    reader.readAsDataURL(file);
                });
            } else {
                fileSelected.textContent = 'Không có tệp nào được chọn';
                fileSelected.style.display = 'block';
                imagePreview.innerHTML = '';
            }
        }

        window.removeSpecificFile = function(index) {
            const newDt = new DataTransfer();
            const files = fileInput.files;
            
            for (let i = 0; i < files.length; i++) {
                if (i !== index) {
                    newDt.items.add(files[i]);
                }
            }
            
            accumulatedFiles = newDt;
            fileInput.files = newDt.files;
            renderPreviews();
        };
    }
});
</script>