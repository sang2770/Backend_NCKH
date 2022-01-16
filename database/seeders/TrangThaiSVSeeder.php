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
            'SoQuyetDinh'       => "45/QD",
            'NgayQuyetDinh'     => "2022-01-14",
            'MaSinhVien'        => "191204226",
        ]);
        Tb_trangthai::insert([
            'SoQuyetDinh'       => "45/QD",
            'NgayQuyetDinh'     => "2022-01-05",
            'MaSinhVien'        => "191204227",
        ]);
    }
}
