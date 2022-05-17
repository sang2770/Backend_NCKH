<?php

namespace Database\Seeders;

use App\Models\Tb_giay_dc_truong;
use Illuminate\Database\Seeder;

class GiayDCTruongSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Tb_giay_dc_truong::insert([
            'MaGiayDC_Truong'  => "1", 
            'SoGioiThieuDC'  => "56",
            'NgayCap'  => "2021-12-10", 
            'NgayHH'  => "2021-12-20",
            'NoiChuyenVe'  => "Cầu giấy, Hà Nội",
            'NoiOHienTai'  => "Cầu giấy, HN",
            'LyDo'  => "Đã tốt nghiệp",
            'MaGiayDK' => "6",
        ]);
        Tb_giay_dc_truong::insert([
            'MaGiayDC_Truong'  => "2", 
            'SoGioiThieuDC'  => "55",
            'NgayCap'  => "2021-06-10", 
            'NgayHH'  => "2021-06-20",
            'NoiChuyenVe'  => "Nam Định",
            'NoiOHienTai'  => "Cầu giấy, HN",
            'LyDo'  => "Thôi học",
            'MaGiayDK' => "4",
        ]);
    }
}
