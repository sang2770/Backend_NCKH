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
            'NoiOHienTai' => 'Nam Định',
            'NoiChuyenDen' => 'DH GTVT',
            'LyDo' => 'Trúng tuyển đại học cao đẳng',
            'BanChiHuy' => 'Ban CHQS phường Ý Yên',
            'MaSinhVien' => '191204229',
        ]);
        Tb_giay_dc_diaphuong::insert([
            'SoGioiThieu' => '56', 
            'NgayCap' => '2020-09-21', 
            'NoiOHienTai' => 'Thanh Hóa',
            'NoiChuyenDen' => 'DH GTVT',
            'LyDo' => 'Trúng tuyển đại học cao đẳng',
            'BanChiHuy' => 'Ban CHQS huyện Vĩnh Lộc ',
            'MaSinhVien' => '191204228',
        ]);
        Tb_giay_dc_diaphuong::insert([
            'SoGioiThieu' => '80', 
            'NgayCap' => '2020-04-02', 
            'NoiOHienTai' => 'Hà Nội',
            'NoiChuyenDen' => 'DH GTVT',
            'LyDo' => 'Trúng tuyển đại học cao đẳng',
            'BanChiHuy' => 'Ban CHQS phường Quan Hoa',
            'MaSinhVien' => '191204227',
        ]);
        Tb_giay_dc_diaphuong::insert([
            'SoGioiThieu' => '45', 
            'NgayCap' => '2020-04-02', 
            'NoiOHienTai' => 'Hà Nội',
            'NoiChuyenDen' => 'DH GTVT',
            'LyDo' => 'Trúng tuyển đại học cao đẳng',
            'BanChiHuy' => 'Ban CHQS phường Quan Hoa',
            'MaSinhVien' => '191204226',
        ]);
        Tb_giay_dc_diaphuong::insert([
            'SoGioiThieu' => '50', 
            'NgayCap' => '2020-04-02', 
            'NoiOHienTai' => 'Hà Nội',
            'NoiChuyenDen' => 'DH GTVT',
            'LyDo' => 'Trúng tuyển đại học cao đẳng',
            'BanChiHuy' => 'Ban CHQS phường Quan Hoa',
            'MaSinhVien' => '191204225',
        ]);
        Tb_giay_dc_diaphuong::insert([
            'SoGioiThieu' => '56', 
            'NgayCap' => '2020-04-02', 
            'NoiOHienTai' => 'Hà Nội',
            'NoiChuyenDen' => 'DH GTVT',
            'LyDo' => 'Trúng tuyển đại học cao đẳng',
            'BanChiHuy' => 'Ban CHQS phường Quan Hoa',
            'MaSinhVien' => '191204224',
        ]);
    }
}
