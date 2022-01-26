<?php

namespace App\Imports;

use App\Models\Tb_Khoa;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class MajorImport implements ToModel, WithHeadingRow, WithChunkReading, SkipsEmptyRows, WithValidation
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // var_dump($row);
        try {
            if (empty($row['tt'])) {
                return null;
            }
            return [
                new Tb_Khoa([
                    'TenKhoa' => $row['ten_khoa'],
                    'DiaChi' => $row['dia_chi'],
                    'SoDienThoai' => $row['so_dien_thoai'],
                ])
            ];
        } catch (\Throwable $th) {
            throw $th;
        }
    }
    public function  rules(): array
    {
        return [
            '*.ten_khoa' => 'required'
        ];
    }
    public function chunkSize(): int
    {
        return 500;
    }
}
