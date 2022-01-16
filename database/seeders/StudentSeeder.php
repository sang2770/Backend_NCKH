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
            'MaSinhVien'        => '191204228',
            'HoTen'             => 'Nguyễn Văn Hoàng',
            'NgaySinh'          => '2001-10-15',
            'NoiSinh'           => 'Hà Nội',
            'GioiTinh'          => 'Nam',
            'DanToc'            => 'Kinh',
            'TonGiao'           => 'Không',
            'QuocTich'          => 'Việt Nam',
            'SoCMTND'           => '123456436',
            'NgayCapCMTND'      => '2019-09-02',
            'NoiCapCMTND'       => 'CA Hà Nội',
            'DiaChiBaoTin'      => 'Hà Nội',
            'SDT'               => '0382963165',
            'Email'             => 'hoang@gmail.com',
            'HoKhauTinh'        => 'Hà Nội',
            'HoKhauHuyen'       => 'Cầu giấy',
            'HoKhauXaPhuong'    => 'Quan Hoa',
            'TinhTrangSinhVien' => 'Đang học',
            'HeDaoTao'          => 'Hệ đào tạo chính quy',
            'MaLop'             => '1',
        ]);
        Tb_sinhvien::insert([
            'MaSinhVien'        => '191204227',
            'HoTen'             => 'Nguyễn Văn Sang',
            'NgaySinh'          => '2001-10-26',
            'NoiSinh'           => 'Hà Nội',
            'GioiTinh'          => 'Nam',
            'DanToc'            => 'Kinh',
            'TonGiao'           => 'Không',
            'QuocTich'          => 'Việt Nam',
            'SoCMTND'           => '123456436',
            'NgayCapCMTND'      => '2019-11-20',
            'NoiCapCMTND'       => 'CA Hà Nội',
            'DiaChiBaoTin'      => 'Hà Nội',
            'SDT'               => '0382963168',
            'Email'             => 'sangml@gmail.com',
            'HoKhauTinh'        => 'Hà Nội',
            'HoKhauHuyen'       => 'Đông Anh',
            'HoKhauXaPhuong'    => 'ABCD',
            'TinhTrangSinhVien' => 'Đang học',
            'HeDaoTao'          => 'Hệ đào tạo chính quy',
            'MaLop'             => '2',
        ]);
        Tb_sinhvien::insert([
            'MaSinhVien'        => '191204226',
            'HoTen'             => 'Nguyễn Nam',
            'NgaySinh'          => '2001-05-21',
            'NoiSinh'           => 'Thanh Hóa',
            'GioiTinh'          => 'Nam',
            'DanToc'            => 'Kinh',
            'TonGiao'           => 'Không',
            'QuocTich'          => 'Việt Nam',
            'SoCMTND'           => '123456436',
            'NgayCapCMTND'      => '2019-11-20',
            'NoiCapCMTND'       => 'CA Thanh Hóa',
            'DiaChiBaoTin'      => 'Vĩnh Lộc Thanh hóa',
            'SDT'               => '0382963168',
            'Email'             => 'nam@gmail.com',
            'HoKhauTinh'        => 'Thanh Hóa',
            'HoKhauHuyen'       => 'Vĩnh Lộc',
            'HoKhauXaPhuong'    => 'Vĩnh Quang',
            'TinhTrangSinhVien' => 'Đang học',
            'HeDaoTao'          => 'Hệ đào tạo chính quy',
            'MaLop'             => '3',
        ]);
    }
}
