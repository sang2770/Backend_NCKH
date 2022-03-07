<?php

namespace Database\Seeders;

use App\Models\Tb_trangthai;
use Illuminate\Database\Seeder;

class TrangThaiSVSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Tb_trangthai::insert([
            'SoQuyetDinh'       => "40/QD",
            'NgayQuyetDinh'     => "2021-01-01",
            'MaSinhVien'        => "191204226",
        ]);
        Tb_trangthai::insert([
            'SoQuyetDinh'       => "50/QD",
            'NgayQuyetDinh'     => "2022-05-10",
            'MaSinhVien'        => "191204227",
        ]);
        Tb_trangthai::insert([
            'SoQuyetDinh'       => "45/QD",
            'NgayQuyetDinh'     => "2021-02-14",
            'MaSinhVien'        => "191204228",
        ]);
        Tb_trangthai::insert([
            'SoQuyetDinh'       => "55/QD",
            'NgayQuyetDinh'     => "2022-01-05",
            'MaSinhVien'        => "191204229",
        ]);
    }
}
