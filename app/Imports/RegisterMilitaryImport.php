<?php

namespace App\Imports;

use App\Models\Tb_giay_cn_dangky;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithValidation;

class RegisterMilitaryImport implements ToModel, WithHeadingRow, WithChunkReading, SkipsEmptyRows, WithValidation
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function headingRow() : int
    {
        return 1;
    }
    public function model(array $row)
    {
        return new Tb_giay_cn_dangky([
            'SoDangKy'          => $row['so_dang_ky'],
            'NgayDangKy' => \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['ngay_dang_ky'])->format('Y-m-d'),
            'NoiDangKy'         => $row['noi_dang_ky'],
            'DiaChiThuongTru'   => $row['dia_chi_thuong_tru'],
            'NgayNop' => \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['ngay_nop'])->format('Y-m-d'),
            'MaSinhVien'        => $row['ma_sinh_vien'],
        ]);
    }

    public function  rules(): array
    {
        return [
            "*.so_dang_ky"          => "required",
            '*.ngay_dang_ky'        => "required",
            "*.noi_dang_ky"         => "required",
            "*.dia_chi_thuong_tru"  => "required",
            "*.ngay_nop"            => "required",
            "*.ma_sinh_vien"        => "required",
        ];
    }
    
    public function chunkSize(): int
    {
        return 500;
    }
}
