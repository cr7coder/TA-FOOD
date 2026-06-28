<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\DoiTacVanChuyen;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class DoiTacVanChuyenController extends Controller
{
    // Middleware được áp dụng trong routes, không cần constructor

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $doiTacs = DoiTacVanChuyen::orderBy('created_at', 'desc')->paginate(10);
        return view('admin.carriers.index', compact('doiTacs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.carriers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'ten_doi_tac' => 'required|string|max:255',
            'so_dien_thoai' => 'required|string|max:20|regex:/^[0-9+\-\s()]+$/',
            'dia_chi_tru_so' => 'required|string',
            'email_lien_he' => 'required|email|max:255|unique:doi_tac_van_chuyens,email_lien_he'
        ], [
            'ten_doi_tac.required' => 'Tên đối tác là bắt buộc.',
            'ten_doi_tac.max' => 'Tên đối tác không được vượt quá 255 ký tự.',
            'so_dien_thoai.required' => 'Số điện thoại là bắt buộc.',
            'so_dien_thoai.regex' => 'Số điện thoại không đúng định dạng.',
            'dia_chi_tru_so.required' => 'Địa chỉ trụ sở là bắt buộc.',
            'email_lien_he.required' => 'Email liên hệ là bắt buộc.',
            'email_lien_he.email' => 'Email không đúng định dạng.',
            'email_lien_he.unique' => 'Email này đã được sử dụng bởi đối tác khác.'
        ]);

        DoiTacVanChuyen::create($request->all());

        return redirect()->route('admin.doi-tac-van-chuyen.index')
            ->with('success', 'Thêm đối tác vận chuyển thành công!');
    }

    /**
     * Display the specified resource.
     */
    public function show(DoiTacVanChuyen $doiTacVanChuyen)
    {
        return view('admin.carriers.show', compact('doiTacVanChuyen'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DoiTacVanChuyen $doiTacVanChuyen)
    {
        return view('admin.carriers.edit', compact('doiTacVanChuyen'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DoiTacVanChuyen $doiTacVanChuyen)
    {
        $request->validate([
            'ten_doi_tac' => 'required|string|max:255',
            'so_dien_thoai' => 'required|string|max:20|regex:/^[0-9+\-\s()]+$/',
            'dia_chi_tru_so' => 'required|string',
            'email_lien_he' => 'required|email|max:255|unique:doi_tac_van_chuyens,email_lien_he,' . $doiTacVanChuyen->id
        ], [
            'ten_doi_tac.required' => 'Tên đối tác là bắt buộc.',
            'ten_doi_tac.max' => 'Tên đối tác không được vượt quá 255 ký tự.',
            'so_dien_thoai.required' => 'Số điện thoại là bắt buộc.',
            'so_dien_thoai.regex' => 'Số điện thoại không đúng định dạng.',
            'dia_chi_tru_so.required' => 'Địa chỉ trụ sở là bắt buộc.',
            'email_lien_he.required' => 'Email liên hệ là bắt buộc.',
            'email_lien_he.email' => 'Email không đúng định dạng.',
            'email_lien_he.unique' => 'Email này đã được sử dụng bởi đối tác khác.'
        ]);

        $doiTacVanChuyen->update($request->all());

        return redirect()->route('admin.doi-tac-van-chuyen.index')
            ->with('success', 'Cập nhật đối tác vận chuyển thành công!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DoiTacVanChuyen $doiTacVanChuyen)
    {
        $doiTacVanChuyen->delete();

        return redirect()->route('admin.doi-tac-van-chuyen.index')
            ->with('success', 'Xóa đối tác vận chuyển thành công!');
    }
}
