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
            'MaGiayDK'          => '1',
            'SoDangKy'          => '45/DK',
            'NgayDangKy'        => '2020-10-10',
            'NoiDangKy'         => 'Hà Nội',
            'DiaChiThuongTru'   => 'Hà Nội',
            'NgayNop'           => '2021-10-10',
            'MaSinhVien'        => '191204226'
        ]);

        Tb_giay_cn_dangky::insert([
            'MaGiayDK'          => '2',
            'SoDangKy'          => '50/DK',
            'NgayDangKy'        => '2019-09-20',
            'NoiDangKy'         => 'Thanh Hóa',
            'DiaChiThuongTru'   => 'Thanh Hóa',
            'NgayNop'           => '2022-10-20',
            'MaSinhVien'        => '191204227'
        ]);
        Tb_giay_cn_dangky::insert([
            'MaGiayDK'          => '3',
            'SoDangKy'          => '135/DK',
            'NgayDangKy'        => '2019-05-05',
            'NoiDangKy'         => 'Nam Định',
            'DiaChiThuongTru'   => 'Hải Hậu, Nam Định',
            'NgayNop'           => '2022-10-05',
            'MaSinhVien'        => '191204228'
        ]);
    }
}
