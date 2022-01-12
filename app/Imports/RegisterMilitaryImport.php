<?php

namespace App\Imports;

use App\Models\Tb_giay_cn_dangky;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class RegisterMilitaryImport implements ToModel, WithHeadingRow, WithChunkReading
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        if (!isset($row[0])) {
            return null;
        }

        return new Tb_giay_cn_dangky([
            'SoDangKy'          => $row[0],
            'NgayDangKy'        => $row[1],
            'NoiDangKy'         => $row[2],
            'DiaChiThuongTru'   => $row[3],
            'NgayNop'           => $row[4],
            'MaSinhVien'        => $row[5],
        ]);
    }
    
    public function chunkSize(): int
    {
        return 500;
    }
}
