<?php

namespace Database\Seeders;

use App\Models\Tb_giay_cn_dangky;
use Doctrine\DBAL\Schema\Schema;
use Illuminate\Database\Seeder;

class GiayCNDKSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        Tb_giay_cn_dangky::insert([
            'MaGiayDK'          => '5',
            'SoDangKy'          => '135/DK',
            'NgayDangKy'        => '2019-05-05',
            'NoiDangKy'         => 'Nam Định',
            'DiaChiThuongTru'   => 'Hải Hậu, Nam Định',
            'NgayNop'           => '2022-10-05',
            'MaSinhVien'        => '211200553'
        ]);
    }
}
