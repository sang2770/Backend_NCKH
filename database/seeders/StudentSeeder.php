<?php

namespace Database\Seeders;

use App\Models\Tb_sinhvien;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Tb_sinhvien::insert([
            'MaSinhVien'        => '191204227',
            'HoTen'             => 'Nguyễn Văn Hoàng',
            'NgaySinh'          => '2001-10-09',
            'NoiSinh'           => 'Nam Định',
            'GioiTinh'          => 'Nam',
            'DanToc'            => 'Kinh',
            'TonGiao'           => 'Không',
            'QuocTich'          => 'Việt Nam',
            'SoCMTND'           => '12345678',
            'NgayCapCMTND'      => '10-10-2019',
            'NoiCapCMTND'       => 'Thanh Hóa',
            'DiaChiBaoTin'      => 'Nam Định',
            'SDT'               => '0382963146',
            'Email'             => 'sangml@gmail.com',
            'HoKhauTinh'        => 'Nam Định',
            'HoKhauHuyen'       => 'Hải Hậu',
            'HoKhauXaPhuong'    => 'Hải Hậu',
            'TinhTrangSinhVien' => 'Đang học',
            'HeDaoTao'          => 'Hệ đào tạo chính quy',
            'MaLop'             => '1',
        ]);
    }
}
