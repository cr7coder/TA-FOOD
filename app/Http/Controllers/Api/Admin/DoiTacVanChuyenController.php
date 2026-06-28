<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\DoiTacVanChuyen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DoiTacVanChuyenController extends Controller
{
    /**
     * Check if user is admin
     */
    private function checkAdmin()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user || !$user->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access. Only admins can access this resource.'
            ], 403);
        }
        return null;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($response = $this->checkAdmin()) return $response;

        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');

        $query = DoiTacVanChuyen::orderBy('created_at', 'desc');

        if ($search) {
            $query->where('ten_doi_tac', 'like', '%' . $search . '%')
                  ->orWhere('email_lien_he', 'like', '%' . $search . '%')
                  ->orWhere('so_dien_thoai', 'like', '%' . $search . '%');
        }

        $doiTacs = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => [
                'items' => $doiTacs->items(),
                'pagination' => [
                    'current_page' => $doiTacs->currentPage(),
                    'last_page' => $doiTacs->lastPage(),
                    'per_page' => $doiTacs->perPage(),
                    'total' => $doiTacs->total(),
                ]
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if ($response = $this->checkAdmin()) return $response;

        try {
            $validated = $request->validate([
                'ten_doi_tac' => 'required|string|max:255',
                'so_dien_thoai' => 'required|string|max:20|regex:/^[0-9+\-\s()]+$/|unique:doi_tac_van_chuyens,so_dien_thoai',
                'dia_chi_tru_so' => 'required|string',
                'email_lien_he' => 'required|email|max:255|unique:doi_tac_van_chuyens,email_lien_he',
                'phi_van_chuyen' => 'nullable|numeric|min:0',
                'phi_km' => 'nullable|numeric|min:0',
                'nguoi_lien_he' => 'required|string|max:255',
                'trang_thai' => 'nullable|string|max:50'
            ], [
                'ten_doi_tac.required' => 'Tên đối tác là bắt buộc.',
                'ten_doi_tac.max' => 'Tên đối tác không được vượt quá 255 ký tự.',
                'so_dien_thoai.required' => 'Số điện thoại là bắt buộc.',
                'so_dien_thoai.regex' => 'Số điện thoại không đúng định dạng.',
                'so_dien_thoai.unique' => 'Số điện thoại này đã được sử dụng bởi đối tác khác.',
                'dia_chi_tru_so.required' => 'Địa chỉ trụ sở là bắt buộc.',
                'email_lien_he.required' => 'Email liên hệ là bắt buộc.',
                'email_lien_he.email' => 'Email không đúng định dạng.',
                'email_lien_he.unique' => 'Email này đã được sử dụng bởi đối tác khác.',
                'phi_van_chuyen.numeric' => 'Phí vận chuyển phải là số.',
                'phi_van_chuyen.min' => 'Phí vận chuyển không được âm.',
                'phi_km.numeric' => 'Phí/km phải là số.',
                'phi_km.min' => 'Phí/km không được âm.',
                'nguoi_lien_he.required' => 'Tên người liên hệ là bắt buộc.',
                'nguoi_lien_he.max' => 'Tên người liên hệ không được vượt quá 255 ký tự.'
            ]);

            $doiTac = DoiTacVanChuyen::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Thêm đối tác vận chuyển thành công!',
                'data' => $doiTac
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        if ($response = $this->checkAdmin()) return $response;

        $doiTac = DoiTacVanChuyen::find($id);

        if (!$doiTac) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy đối tác vận chuyển.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $doiTac
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        if ($response = $this->checkAdmin()) return $response;

        $doiTac = DoiTacVanChuyen::find($id);

        if (!$doiTac) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy đối tác vận chuyển.'
            ], 404);
        }

        try {
            $validated = $request->validate([
                'ten_doi_tac' => 'required|string|max:255',
                'so_dien_thoai' => 'required|string|max:20|regex:/^[0-9+\-\s()]+$/|unique:doi_tac_van_chuyens,so_dien_thoai,' . $id,
                'dia_chi_tru_so' => 'required|string',
                'email_lien_he' => 'required|email|max:255|unique:doi_tac_van_chuyens,email_lien_he,' . $id,
                'phi_van_chuyen' => 'nullable|numeric|min:0',
                'phi_km' => 'nullable|numeric|min:0',
                'nguoi_lien_he' => 'required|string|max:255',
                'trang_thai' => 'nullable|string|max:50'
            ], [
                'ten_doi_tac.required' => 'Tên đối tác là bắt buộc.',
                'ten_doi_tac.max' => 'Tên đối tác không được vượt quá 255 ký tự.',
                'so_dien_thoai.required' => 'Số điện thoại là bắt buộc.',
                'so_dien_thoai.regex' => 'Số điện thoại không đúng định dạng.',
                'so_dien_thoai.unique' => 'Số điện thoại này đã được sử dụng bởi đối tác khác.',
                'dia_chi_tru_so.required' => 'Địa chỉ trụ sở là bắt buộc.',
                'email_lien_he.required' => 'Email liên hệ là bắt buộc.',
                'email_lien_he.email' => 'Email không đúng định dạng.',
                'email_lien_he.unique' => 'Email này đã được sử dụng bởi đối tác khác.',
                'phi_van_chuyen.numeric' => 'Phí vận chuyển phải là số.',
                'phi_van_chuyen.min' => 'Phí vận chuyển không được âm.',
                'phi_km.numeric' => 'Phí/km phải là số.',
                'phi_km.min' => 'Phí/km không được âm.',
                'nguoi_lien_he.required' => 'Tên người liên hệ là bắt buộc.',
                'nguoi_lien_he.max' => 'Tên người liên hệ không được vượt quá 255 ký tự.'
            ]);

            $doiTac->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật đối tác vận chuyển thành công!',
                'data' => $doiTac
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        if ($response = $this->checkAdmin()) return $response;

        $doiTac = DoiTacVanChuyen::find($id);

        if (!$doiTac) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy đối tác vận chuyển.'
            ], 404);
        }

        try {
            $doiTac->delete();

            return response()->json([
                'success' => true,
                'message' => 'Xóa đối tác vận chuyển thành công!'
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
