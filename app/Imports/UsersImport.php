<?php

namespace App\Imports;

use App\Models\Tb_cmtnd;
use App\Models\Tb_lop;
use App\Models\Tb_sinhvien;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Helper\Helper;
use App\Models\Tb_tk_sinhvien;

class UsersImport implements ToModel, WithHeadingRow, WithChunkReading
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $TenLop = $row['lop'];
        $MaLop = Tb_lop::where('TenLop', $TenLop)->value('MaLop');
        // Create Tài Khoản
        $TaiKhoan = Helper::CreateUsers(["MaSinhVien" => $row["ma_sinh_vien"], "NgaySinh" => $row["ngay_sinh"], "HoTen" => $row["ho_ten"]]);
        // var_dump($row);
        // var_dump($TaiKhoan);
        return [
            new Tb_sinhvien([
                'MaSinhVien' => $row['ma_sinh_vien'],
                'HoTen' => $row['ho_ten'],
                'NgaySinh' => $row['ngay_sinh'],
                'NoiSinh' => $row['noi_sinh'],
                'GioiTinh' => $row['gioi_tinh'],
                'DanToc' => $row["dan_toc"],
                'TonGiao' => $row['ton_giao'],
                'QuocTich' => $row['quoc_tich'],
                'DiaChiBaoTin' => $row['dia_chi_khi_bao_tin'],
                'SDT' => $row['so_dien_thoai'],
                'Email' => $row['email'],
                'HoKhauTinh' => $row['ho_khau_tinhtp'],
                'HoKhauHuyen' => $row['ho_khau_quanhuyen'],
                'HoKhauXaPhuong' => $row['ho_khau_xaphuong'],
                'TinhTrangSinhVien' => $row['tinh_trang_sinh_vien'],
                'HeDaoTao' => $row['he_dao_tao'],
                'MaLop' => $MaLop,
            ]),
            new Tb_cmtnd(
                [
                    'SoCMTND' => $row['chung_minh_nhan_dan'],
                    'NoiCap_CMTND' => $row['noi_cap_cmnd'],
                    'NgayCap_CMTND' => $row['ngay_cap_cmnd'],
                    'MaSinhVien' => $row['ma_sinh_vien']
                ]
            ),
            new Tb_tk_sinhvien(
                $TaiKhoan
            )
        ];
    }

    public function chunkSize(): int
    {
        return 500;
    }
}
