<?php

namespace Database\Seeders;

use App\Models\Tb_thongbaochinh;
use Illuminate\Database\Seeder;

class ThongBaoChinhSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Tb_thongbaochinh::insert([
            'TieuDeTB'      => 'Tiêu đề thông báo 1',
            'NoiDungTB'     => 'Nội dung thông báo 1', 
            'FileName'      => '',
            'ThoiGianTao'   => '2020-10-10',
        ]);
        Tb_thongbaochinh::insert([
            'TieuDeTB'      => 'Tiêu đề thông báo 2',
            'NoiDungTB'     => 'Nội dung thông báo 2', 
            'FileName'      => '',
            'ThoiGianTao'   => '2020-10-20',
        ]);
        Tb_thongbaochinh::insert([
            'TieuDeTB'      => 'Tiêu đề thông báo 3',
            'NoiDungTB'     => 'Nội dung thông báo 3', 
            'FileName'      => '',
            'ThoiGianTao'   => '2020-12-20',
        ]);
    }
}
