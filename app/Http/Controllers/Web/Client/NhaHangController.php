<?php

namespace App\Http\Controllers\Web\Client;

use App\Http\Controllers\Controller;

use App\Models\NhaHang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NhaHangController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index() {}

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $activeCategoryNames = \App\Models\DanhMuc::where('TrangThai', 'Hoạt động')->pluck('TenDanhMuc')->toArray();

        $restaurant = NhaHang::with(['monAn' => function ($q) use ($activeCategoryNames) {
            $q->where('TrangThai', 'Còn bán')->whereIn('DanhMuc', $activeCategoryNames);
        }])->where('MaNhaHang', $id)->firstOrFail();

        $foods = $restaurant->monAn;

        return view('client.restaurants.show', compact('restaurant', 'foods'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(NhaHang $nhaHang)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, NhaHang $nhaHang)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(NhaHang $nhaHang)
    {
        //
    }
}

