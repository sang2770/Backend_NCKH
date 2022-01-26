<?php

namespace Database\Seeders;

use App\Models\Tb_giay_dc_diaphuong;
use Illuminate\Database\Seeder;

class DC_DiaPhuongSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Tb_giay_dc_diaphuong::insert([
            'SoGioiThieu' => '88', 
            'NgayCap' => '2020-10-20', 
            'NgayHH' => '2020-11-10',
            'NoiOHienTai' => 'Nam Định',
            'NoiChuyenDen' => 'DH GTVT',
            'LyDo' => 'Trúng tuyển đại học cao đẳng',
            'BanChiHuy' => 'Ban CHQS phường Ý Yên',
            'MaGiayDK' => '1',
        ]);
        Tb_giay_dc_diaphuong::insert([
            'SoGioiThieu' => '56', 
            'NgayCap' => '2020-09-21', 
            'NgayHH' => '2020-10-10',
            'NoiOHienTai' => 'Thanh Hóa',
            'NoiChuyenDen' => 'DH GTVT',
            'LyDo' => 'Trúng tuyển đại học cao đẳng',
            'BanChiHuy' => 'Ban CHQS huyện Vĩnh Lộc ',
            'MaGiayDK' => '2',
        ]);
        Tb_giay_dc_diaphuong::insert([
            'SoGioiThieu' => '80', 
            'NgayCap' => '2020-04-02', 
            'NgayHH' => '2020-05-02',
            'NoiOHienTai' => 'Hà Nội',
            'NoiChuyenDen' => 'DH GTVT',
            'LyDo' => 'Trúng tuyển đại học cao đẳng',
            'BanChiHuy' => 'Ban CHQS phường Quan Hoa',
            'MaGiayDK' => '3',
        ]);

    }
}
