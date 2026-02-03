<?php

namespace App\Http\Controllers;

use App\Models\MonAn;
use App\Models\NhaHang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class MonAnController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // Hiển thị danh sách món ăn
    public function menu(Request $request)
    {
        $foods = MonAn::paginate(12);

        $restaurants = NhaHang::withCount(['monAn' => function ($q) {
            $q->where('TrangThai', 'Còn bán');
        }])
            ->orderBy('TenNhaHang')
            ->get();

        return view('foods.index', compact('foods', 'restaurants'));
    }

    private array $danhMuc = ['Cơm', 'Bún', 'Phở', 'Mì', 'Trà'];
    private array $trangThai = ['Còn bán', 'Ngừng bán'];

    public function index(Request $request)
    {
        $sellerId = Auth::user()->MaNguoiDung;

        $query = MonAn::with('nhaHang')->ofSeller($sellerId);

        if ($request->filled('MaNhaHang')) {
            $query->where('MaNhaHang', $request->MaNhaHang);
        }

        if ($request->filled('search')) {
            $query->where('TenMonAn', 'like', '%' . trim($request->search) . '%');
        }

        $monAns = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        return view('seller.foods.index', [
            'monAns'    => $monAns,
            'danhMuc'   => $this->danhMuc,
            'trangThai' => $this->trangThai,
        ]);
    }

    public function create()
    {
        $sellerId = Auth::user()->MaNguoiDung;

        $nhaHangs = NhaHang::where('MaNguoiDung', $sellerId)
            ->orderBy('MaNhaHang', 'desc')
            ->get(['MaNhaHang']); // nếu có TenNhaHang thì lấy thêm

        return view('seller.foods.create', [
            'nhaHangs'  => $nhaHangs,
            'danhMuc'   => $this->danhMuc,
            'trangThai' => $this->trangThai,
        ]);
    }

    // public function store(Request $request)
    // {
    //     $sellerId = Auth::user()->MaNguoiDung;
    //     $allowedRestaurantIds = NhaHang::where('MaNguoiDung', $sellerId)->pluck('MaNhaHang')->all();

    //     $validated = $request->validate([
    //         'MaNhaHang' => ['required', 'integer', Rule::in($allowedRestaurantIds)],
    //         'TenMonAn'  => ['required', 'string', 'max:150'],
    //         'DanhMuc'   => ['required', Rule::in($this->danhMuc)],
    //         'MoTa'      => ['nullable', 'string', 'max:255'],
    //         'Gia'       => ['required', 'numeric', 'min:0'],
    //         'TrangThai' => ['required', Rule::in($this->trangThai)],
    //         'HinhAnh'   => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
    //     ], [
    //         'MaNhaHang.in' => 'Bạn không có quyền với nhà hàng đã chọn.',
    //     ]);

    //     $filename = null;
    //     if ($request->hasFile('HinhAnh')) {
    //         $filename = $this->saveImageToPublic($request->file('HinhAnh'));
    //     }

    //     MonAn::create([
    //         'MaNhaHang' => $validated['MaNhaHang'],
    //         'TenMonAn'  => $validated['TenMonAn'],
    //         'LoaiMon'   => $validated['LoaiMon'],
    //         'MoTa'      => $validated['MoTa'] ?? null,
    //         'Gia'       => $validated['Gia'],
    //         'HinhAnh'   => $filename,
    //         'TrangThai' => $validated['TrangThai'],
    //     ]);

    //     return redirect()->route('seller.foods.index')->with('success', 'Đã thêm món ăn thành công!');
    // }
    public function store(Request $request)
    {
        $sellerId = Auth::user()->MaNguoiDung;
        $maNhaHang = NhaHang::where('MaNguoiDung', $sellerId)->value('MaNhaHang');
        if (!$maNhaHang) {
            return redirect()->back()->withInput()->with('error', 'Tài khoản người bán chưa được gán nhà hàng.');
        }
        $validated = $request->validate([
            // 2E.1, 2E.2, 2E.3, 2E.4
            'TenMonAn'  => [
                'required',
                'string',
                'max:100',
                'regex:/^[\p{L}\p{N}\s\-_]+$/u', // chỉ chữ, số, khoảng trắng, -, _
                Rule::unique(MonAn::class, 'TenMonAn'),
            ],

            // 2E.8, 2E.9
            'DanhMuc'   => ['required', Rule::in($this->danhMuc)],

            // Mô tả có thể để trống, không giới hạn độ dài
            'MoTa'      => ['nullable', 'string'],

            // 2E.5, 2E.6, 2E.7
            'Gia'       => ['required', 'numeric', 'gt:0'],

            // 2E.10: không bắt buộc, nếu không chọn sẽ gán mặc định "Còn bán"
            'TrangThai' => ['nullable', Rule::in($this->trangThai)],

            // 2E.11, 2E.12, 2E.13
            'HinhAnh'   => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:5120'], // 5MB = 5120KB
        ]);
        // , [

        //     'TenMonAn.required'    => 'Tên món ăn không được bỏ trống (2E.1)',
        //     'TenMonAn.max'         => 'Tên món ăn không quá 100 ký tự (2E.2)',
        //     'TenMonAn.regex'       => 'Tên món ăn không được chứa ký tự đặc biệt (2E.3)',
        //     'TenMonAn.unique'      => 'Tên món ăn đã tồn tại trong hệ thống (2E.4)',

        //     'Gia.required'         => 'Giá món ăn không được bỏ trống (2E.5)',
        //     'Gia.gt'               => 'Giá món ăn phải lớn hơn 0 (2E.6)',
        //     'Gia.numeric'          => 'Giá món ăn chỉ được nhập số (2E.7)',

        //     'DanhMuc.required'     => 'Danh mục không được bỏ trống (2E.8)',
        //     'DanhMuc.in'           => 'Danh mục không hợp lệ (2E.9)',

        //     'HinhAnh.required'     => 'Hình ảnh món ăn không được bỏ trống (2E.11)',
        //     'HinhAnh.mimes'        => 'Hình ảnh chỉ hỗ trợ jpg, jpeg, png (2E.12)',
        //     'HinhAnh.max'          => 'Hình ảnh không được vượt quá 5MB (2E.13)',
        // ]);

        // Gán mặc định trạng thái nếu không chọn (2E.10)
        $validated['TrangThai'] = $validated['TrangThai'] ?? 'Còn bán';

        // Lưu ảnh (đã required nên chắc chắn có file)
        $filename = $this->saveImageToPublic($request->file('HinhAnh'));

        MonAn::create([
            'MaNhaHang' => $maNhaHang,
            'TenMonAn'  => $validated['TenMonAn'],
            'DanhMuc'   => $validated['DanhMuc'],
            'MoTa'      => $validated['MoTa'] ?? null,
            'Gia'       => $validated['Gia'],
            'HinhAnh'   => $filename,
            'TrangThai' => $validated['TrangThai'],
        ]);

        return redirect()->route('seller.foods.index')->with('success', 'Đã thêm món ăn thành công!');
    }

    public function edit(MonAn $food)
    {
        $sellerId = Auth::user()->MaNguoiDung;

        if (!$food->nhaHang || $food->nhaHang->owner?->MaNguoiDung !== $sellerId) {
            abort(403, 'Bạn không có quyền sửa món này.');
        }

        $maNhaHang = NhaHang::where('MaNguoiDung', $sellerId)->value('MaNhaHang');

        return view('seller.foods.edit', [
            'food'      => $food,
            'danhMuc'   => $this->danhMuc,
            'trangThai' => $this->trangThai,
            'maNhaHang' => $maNhaHang,
        ]);
    }

    public function update(Request $request, MonAn $food)
    {
        $sellerId = Auth::user()->MaNguoiDung;

        if (!$food->nhaHang || $food->nhaHang->owner?->MaNguoiDung !== $sellerId) {
            abort(403, 'Bạn không có quyền sửa món này.');
        }

        $validated = $request->validate([
            'TenMonAn' => [
                'required',
                'string',
                'max:100',
                'regex:/^[\p{L}\p{N}\s\-_]+$/u',
                Rule::unique(MonAn::class, 'TenMonAn')->ignore($food->MaMonAn, 'MaMonAn'),
            ],
            'DanhMuc' => ['required', Rule::in($this->danhMuc)],
            'MoTa' => ['nullable', 'string'],
            'Gia' => ['required', 'numeric', 'gt:0'],
            'TrangThai' => ['nullable', Rule::in($this->trangThai)],
            'HinhAnh' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
        ]);
        // , [
        //     'TenMonAn.required' => 'Tên món ăn không được bỏ trống (2E.1)',
        //     'TenMonAn.max'      => 'Tên món ăn không quá 100 ký tự (2E.2)',
        //     'TenMonAn.regex'    => 'Tên món ăn không được chứa ký tự đặc biệt (2E.3)',
        //     'TenMonAn.unique'   => 'Tên món ăn đã tồn tại trong hệ thống (2E.4)',

        //     'Gia.required'      => 'Giá món ăn không được bỏ trống (2E.5)',
        //     'Gia.gt'            => 'Giá món ăn phải lớn hơn 0 (2E.6)',
        //     'Gia.numeric'       => 'Giá món ăn chỉ được nhập số (2E.7)',

        //     'DanhMuc.required'  => 'Danh mục không được bỏ trống (2E.8)',
        //     'DanhMuc.in'        => 'Danh mục không hợp lệ (2E.9)',

        //     'HinhAnh.mimes'     => 'Hình ảnh chỉ hỗ trợ jpg, jpeg, png (2E.12)',
        //     'HinhAnh.max'       => 'Hình ảnh không được vượt quá 5MB (2E.13)',
        // ]);

        // 2E.10: mặc định Còn bán nếu không chọn
        $validated['TrangThai'] = $validated['TrangThai'] ?? 'Còn bán';

        // Ảnh mới (nếu có) – hoãn xóa ảnh cũ cho đến khi save DB thành công
        $newImageFilename = null;
        $oldImageFilename = $food->HinhAnh;
        if ($request->hasFile('HinhAnh')) {
            $newImageFilename = $this->saveImageToPublic($request->file('HinhAnh'));
            $food->HinhAnh = $newImageFilename;
        }

        // Gán dữ liệu mới
        $food->TenMonAn  = $validated['TenMonAn'];
        $food->DanhMuc   = $validated['DanhMuc'];
        $food->MoTa      = $validated['MoTa'] ?? null;
        $food->Gia       = $validated['Gia'];
        $food->TrangThai = $validated['TrangThai'];

        // 3E.14: Không thay đổi dữ liệu nào
        // $isDirty = $food->isDirty(['TenMonAn', 'DanhMuc', 'MoTa', 'Gia', 'TrangThai', 'HinhAnh']);
        // if (!$isDirty) {
        //     // Nếu có upload ảnh mới nhưng nội dung giống, vẫn coi là không thay đổi: thu hồi ảnh mới
        //     // if ($newImageFilename) {
        //     //     $this->deleteImageFromPublic($newImageFilename);
        //     //     $food->HinhAnh = $oldImageFilename; // restore
        //     // }
        //     return back()->withInput()->with('warning', 'Không có thông tin nào được cập nhật (3E.14)');
        // }

        try {
            $food->save();

            // Lưu thành công mới xóa ảnh cũ
            if ($newImageFilename && $oldImageFilename && $oldImageFilename !== $newImageFilename) {
                $this->deleteImageFromPublic($oldImageFilename);
            }

            return redirect()->route('seller.foods.index')->with('success', 'Cập nhật món ăn thành công!');
        } catch (\Throwable $e) {
            // 3E.15: Lỗi hệ thống khi lưu
            // Thu hồi ảnh mới tránh rác
            if ($newImageFilename) {
                $this->deleteImageFromPublic($newImageFilename);
                $food->HinhAnh = $oldImageFilename; // restore
            }
            report($e);
            return back()->withInput()->with('error', 'Sửa món thất bại vì lỗi hệ thống. Vui lòng thử lại (3E.15)');
        }
    }
    public function destroy(MonAn $food)
    {
        $sellerId = Auth::user()->MaNguoiDung;

        if (!$food->nhaHang || $food->nhaHang->owner?->MaNguoiDung !== $sellerId) {
            abort(403, 'Bạn không có quyền xóa món này.');
        }

        try {
            // Xóa ảnh trong public/images nếu có
            $this->deleteImageFromPublic($food->HinhAnh);

            // Xóa bản ghi món ăn
            $food->delete();

            return redirect()->route('seller.foods.index')->with('success', 'Đã xóa món ăn thành công!');
        } catch (\Throwable $e) {
            // Nếu có ràng buộc FK (ví dụ món đã có trong đơn hàng), sẽ bắt lỗi tại đây
            return back()->with('error', 'Không thể xóa món ăn. Lý do: ' . $e->getMessage());
        }
    }

    private function saveImageToPublic(\Illuminate\Http\UploadedFile $file): string
    {
        $ext   = strtolower($file->getClientOriginalExtension());
        $name  = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $slug  = Str::slug($name) ?: 'image';
        $final = $slug . '-' . Str::random(8) . '.' . $ext;

        $destination = public_path('images');
        if (!File::exists($destination)) {
            File::makeDirectory($destination, 0755, true);
        }

        $file->move($destination, $final);
        return $final; // chỉ trả về tên file
    }

    private function deleteImageFromPublic(?string $filename): void
    {
        if (!$filename) return;
        $path = public_path('images/' . $filename);
        if (File::exists($path)) {
            File::delete($path);
        }
    }

    /**
     * Hiển thị chi tiết món ăn
     */
    public function detail($id)
    {
        $food = MonAn::with(['nhaHang', 'binhLuans.nguoiDung'])
            ->findOrFail($id);

        // Lấy món ăn liên quan (cùng danh mục)
        $relatedFoods = MonAn::where('DanhMuc', $food->DanhMuc)
            ->where('MaMonAn', '!=', $id)
            ->where('TrangThai', 'Còn bán')
            ->limit(4)
            ->get();

        return view('foods.detail', compact('food', 'relatedFoods'));
    }
}
