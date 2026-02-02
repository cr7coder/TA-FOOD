<?php

namespace App\Http\Controllers;

use App\Models\GiamGia;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class GiamGiaController extends Controller
{
    public function index(Request $request)
    {
        $query = GiamGia::query();

        // Tìm kiếm theo mã code
        if ($request->filled('search')) {
            $query->where('MaCode', 'like', '%' . $request->search . '%');
        }

        // Lọc theo trạng thái
        if ($request->filled('status')) {
            switch ($request->status) {
                case 'active':
                    $query->active();
                    break;
                case 'expired':
                    $query->expired();
                    break;
                case 'upcoming':
                    $query->where('NgayBatDau', '>', Carbon::now()->toDateString());
                    break;
            }
        }

        $vouchers = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('admin.vouchers.index', compact('vouchers'));
    }

    public function create()
    {
        return view('admin.vouchers.create');
    }

    private function getRules(): array
    {
        return [
            'MaCode'           => ['required', 'string', 'max:12', 'alpha_num', 'unique:giam_gia,MaCode'],
            'PhanTram'         => ['required', 'integer', 'min:1', 'max:100'],
            'DonHangToiThieu'  => ['nullable', 'numeric', 'min:0'],
            'SoLanToiDa'       => ['nullable', 'integer', 'min:1'],
            'NgayBatDau'       => ['required', 'date', 'after_or_equal:today'],
            'NgayKetThuc'      => ['required', 'date', 'after_or_equal:NgayBatDau'],
        ];
    }

    private function getMessages(): array
    {
        return [
            // Mã giảm giá
            'MaCode.required'   => 'Mã giảm giá không được bỏ trống',        // 8E.1
            'MaCode.max'        => 'Mã giảm giá không quá 12 ký tự',         // 8E.2
            'MaCode.alpha_num'  => 'Mã giảm giá không chứa ký tự đặc biệt',  // 8E.3
            'MaCode.unique'     => 'Mã giảm giá đã tồn tại',                 // 8E.4

            // Phần trăm giảm
            'PhanTram.required' => 'Phần trăm giảm không được bỏ trống',     // 8E.5
            'PhanTram.min'      => 'Phần trăm giảm phải lớn hơn 0',          // 8E.6
            'PhanTram.max'      => 'Phần trăm giảm tối đa là 100',           // 8E.7
            'PhanTram.integer'  => 'Phần trăm giảm chỉ chấp nhận số nguyên', // 8E.8

            // Đơn hàng tối thiểu
            'DonHangToiThieu.numeric' => 'Đơn hàng tối thiểu phải là số',    // 8E.15
            'DonHangToiThieu.min'     => 'Đơn hàng tối thiểu phải lớn hơn hoặc bằng 0', // 8E.16

            // Số lần tối đa
            'SoLanToiDa.integer' => 'Số lần tối đa phải là số nguyên',       // 8E.17
            'SoLanToiDa.min'     => 'Số lần tối đa phải lớn hơn 0',          // 8E.18

            // Ngày bắt đầu
            'NgayBatDau.required'        => 'Ngày bắt đầu không được bỏ trống',                    // 8E.9
            'NgayBatDau.date'            => 'Ngày bắt đầu không hợp lệ',                          // 8E.10
            'NgayBatDau.after_or_equal'  => 'Ngày bắt đầu phải lớn hơn hoặc bằng ngày hiện tại', // 8E.11

            // Ngày kết thúc
            'NgayKetThuc.required'       => 'Ngày kết thúc không được bỏ trống',                  // 8E.12
            'NgayKetThuc.date'           => 'Ngày kết thúc không hợp lệ',                         // 8E.13
            'NgayKetThuc.after_or_equal' => 'Ngày kết thúc phải sau hoặc bằng ngày bắt đầu',     // 8E.14
        ];
    }

    // Validate server-side cho nút "Tiếp theo", trả JSON lỗi nếu có
    public function validateStep(Request $request)
    {
        $rules = $this->getRules();
        $messages = $this->getMessages();

        $step = (int) $request->input('step', 1);
        $fieldsByStep = [
            1 => ['MaCode', 'PhanTram', 'DonHangToiThieu', 'SoLanToiDa'],
            2 => ['NgayBatDau', 'NgayKetThuc'],
            3 => [],
        ];
        $fields = $fieldsByStep[$step] ?? [];

        $filteredRules = array_intersect_key($rules, array_flip($fields));

        $validator = Validator::make($request->all(), $filteredRules, $messages);

        if ($validator->fails()) {
            return response()->json([
                'ok'     => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        return response()->json(['ok' => true]);
    }

    public function store(Request $request)
    {
        $rules = $this->getRules();
        $messages = $this->getMessages();

        $validated = $request->validate($rules, $messages);

        GiamGia::create($validated);

        return redirect()->route('admin.vouchers.index')
            ->with('success', 'Thêm voucher thành công!');
    }

    public function checkMaCode(Request $request)
    {
        $maCode = $request->query('MaCode');
        $exists = GiamGia::where('MaCode', $maCode)->exists();
        return response()->json(['exists' => $exists]);
    }

    public function destroy(GiamGia $voucher)
    {
        try {
            $id = $voucher->getKey();
            $rows = DB::table('giam_gia')->where('MaGiamGia', $id)->delete();
            $stillExists = GiamGia::useWritePdo()->whereKey($id)->exists();

            if ($rows < 1 || $stillExists) {
                return back()->with('error', 'Xóa voucher thất bại. Bản ghi vẫn còn trong hệ thống.');
            }

            return redirect()->route('admin.vouchers.index')
                ->with('success', 'Xóa voucher thành công!');
        } catch (QueryException $e) {
            return back()->with('error', 'Không thể xóa voucher do ràng buộc dữ liệu (FK). Chi tiết: ' . $e->getMessage());
        } catch (\Throwable $e) {
            return back()->with('error', 'Không thể xóa voucher. Lý do: ' . $e->getMessage());
        }
    }

    public function generateCode()
    {
        do {
            $code = 'VC' . strtoupper(Str::random(8));
        } while (GiamGia::where('MaCode', $code)->exists());

        return response()->json(['code' => $code]);
    }

    public function export()
    {
        $vouchers = GiamGia::all();

        $filename = 'vouchers_' . date('Y-m-d_H-i-s') . '.csv';

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');

        // Header
        fputcsv($output, ['Mã Voucher', 'Phần Trăm', 'Ngày Bắt Đầu', 'Ngày Kết Thúc', 'Trạng Thái']);

        foreach ($vouchers as $voucher) {
            $status = $voucher->isActive() ? 'Còn hiệu lực' : ($voucher->isExpired() ? 'Hết hạn' : 'Chưa kích hoạt');
            fputcsv($output, [
                $voucher->MaCode,
                $voucher->PhanTram . '%',
                Carbon::parse($voucher->NgayBatDau)->format('d/m/Y'),
                Carbon::parse($voucher->NgayKetThuc)->format('d/m/Y'),
                $status
            ]);
        }

        fclose($output);
    }
}
