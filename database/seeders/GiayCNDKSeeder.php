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
            'SoDangKy'          => '135/DK',
            'NgayDangKy'        => '2019-05-05',
            'NoiDangKy'         => 'Nam Định',
            'DiaChiThuongTru'   => 'Hải Hậu, Nam Định',
            'NgayNop'           => '2022-10-05',
            'MaSinhVien'        => '191203366'
        ]);
        Tb_giay_cn_dangky::insert([
            'MaGiayDK'          => '2',
            'SoDangKy'          => '115/DK',
            'NgayDangKy'        => '2019-05-01',
            'NoiDangKy'         => 'Thanh Hóa',
            'DiaChiThuongTru'   => 'Vĩnh Lộc, Thanh Hóa',
            'NgayNop'           => '2022-10-05',
            'MaSinhVien'        => '191204225'
        ]);
        Tb_giay_cn_dangky::insert([
            'MaGiayDK'          => '3',
            'SoDangKy'          => '125/DK',
            'NgayDangKy'        => '2019-10-05',
            'NoiDangKy'         => 'Cầu Giấy',
            'DiaChiThuongTru'   => 'Cầu Giấy, Hà Nội',
            'NgayNop'           => '2022-10-05',
            'MaSinhVien'        => '191204226'
        ]);
        Tb_giay_cn_dangky::insert([
            'MaGiayDK'          => '4',
            'SoDangKy'          => '135/DK',
            'NgayDangKy'        => '2019-05-05',
            'NoiDangKy'         => 'Hải Phòng',
            'DiaChiThuongTru'   => 'Hải Phòng',
            'NgayNop'           => '2022-10-05',
            'MaSinhVien'        => '191204227'
        ]);
        Tb_giay_cn_dangky::insert([
            'MaGiayDK'          => '5',
            'SoDangKy'          => '135/DK',
            'NgayDangKy'        => '2021-05-05',
            'NoiDangKy'         => 'Nam Định',
            'DiaChiThuongTru'   => 'Hải Hậu, Nam Định',
            'NgayNop'           => '2022-10-05',
            'MaSinhVien'        => '191204228'
        ]);
        Tb_giay_cn_dangky::insert([
            'MaGiayDK'          => '6',
            'SoDangKy'          => '135/DK',
            'NgayDangKy'        => '2020-02-05',
            'NoiDangKy'         => 'Hà Nội',
            'DiaChiThuongTru'   => 'Hà Nội',
            'NgayNop'           => '2022-10-05',
            'MaSinhVien'        => '191204229'
        ]);
    }
}
