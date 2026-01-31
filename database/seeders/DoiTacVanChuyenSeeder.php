<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DoiTacVanChuyen;

class DoiTacVanChuyenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $doiTacs = [
            [
                'ten_doi_tac' => 'Giao Hàng Nhanh (GHN)',
                'so_dien_thoai' => '1900545415',
                'dia_chi_tru_so' => 'Tầng 8, Tòa nhà Daeha Business Center, 360 Cống Quỳnh, Phường Phạm Ngũ Lão, Quận 1, TP.HCM',
                'email_lien_he' => 'contact@ghn.vn',
                'phi_van_chuyen' => 25000,
                'trang_thai' => 'Hoạt động'
            ],
            [
                'ten_doi_tac' => 'Giao Hàng Tiết Kiệm (GHTK)',
                'so_dien_thoai' => '1900636677',
                'dia_chi_tru_so' => '52 Út Tịch, Phường 4, Quận Tân Bình, TP.HCM',
                'email_lien_he' => 'hotro@giaohangtietkiem.vn',
                'phi_van_chuyen' => 22000,
                'trang_thai' => 'Hoạt động'
            ],
            [
                'ten_doi_tac' => 'ViettelPost',
                'so_dien_thoai' => '1800103086',
                'dia_chi_tru_so' => '8 Phạm Hùng, Mỹ Đình 2, Nam Từ Liêm, Hà Nội',
                'email_lien_he' => 'info@viettelpost.vn',
                'phi_van_chuyen' => 28000,
                'trang_thai' => 'Hoạt động'
            ],
            [
                'ten_doi_tac' => 'J&T Express',
                'so_dien_thoai' => '1900638585',
                'dia_chi_tru_so' => 'Tầng 5, Tòa nhà Itaxa, 125 Nguyễn Văn Trỗi, Phường 11, Quận Phú Nhuận, TP.HCM',
                'email_lien_he' => 'info@jtexpress.vn',
                'phi_van_chuyen' => 20000,
                'trang_thai' => 'Hoạt động'
            ],
            [
                'ten_doi_tac' => 'Best Express',
                'so_dien_thoai' => '1900545467',
                'dia_chi_tru_so' => 'Lầu 4, Tòa nhà IDMC, 14 Nguyễn Đình Chiểu, Phường Đa Kao, Quận 1, TP.HCM',
                'email_lien_he' => 'cs@best-inc.vn',
                'phi_van_chuyen' => 23000,
                'trang_thai' => 'Hoạt động'
            ]
        ];

        foreach ($doiTacs as $doiTac) {
            DoiTacVanChuyen::create($doiTac);
        }
    }
}
