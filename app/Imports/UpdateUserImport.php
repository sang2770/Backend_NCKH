<?php

namespace App\Imports;

use App\Models\Tb_lop;
use App\Models\Tb_sinhvien;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class UpdateUserImport implements ToCollection, WithHeadingRow, WithChunkReading, SkipsEmptyRows, WithValidation
{
    public function collection(Collection $rows)
    {

        foreach ($rows as $row) {
            // var_dump($row);
            if (empty($row['tt'])) {
                return null;
            }
            $TenLop = $row['ten_lop'];
            $MaLop = Tb_lop::where('TenLop', $TenLop)->value('MaLop');
            Tb_sinhvien::where('MaSinhVien', $row['ma_sinh_vien'])->update([
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
            ]);
        }
    }
    public function  rules(): array
    {
        return [
            "*.ma_sinh_vien" => "required",
            '*.ho_ten' => "required",
            "*.ngay_sinh" => "required",
            "*.noi_sinh" => "required",
            "*.email" => "required|email",
            "*.gioi_tinh" => "required",
            "*.ton_giao" => "required",
            "*.quoc_tich" => "required",
            "*.dan_toc" => "required",
            "*.so_dien_thoai" => "required|digits:10",
            "*.dia_chi_khi_bao_tin" => "required",
            "*.he_dao_tao" => "required",
            "*.tinh_trang_sinh_vien" => "required",
            "*.ho_khau_tinhtp" => "required",
            "*.ho_khau_quanhuyen" => "required",
            "*.ho_khau_xaphuong" => "required",
            "*.ten_lop" => "required"
        ];
    }
    public function chunkSize(): int
    {
        return 500;
    }
}
