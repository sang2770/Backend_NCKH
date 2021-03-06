<?php

namespace App\Imports;
use App\Models\Tb_khoa;
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
        try {
            if (empty($row['tt'])) {
                return null;
            }
            return [
                new Tb_khoa([
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
    public function customValidationMessages()
    {
        return [
            'ten_khoa.required' => 'Tên khoa là bắt buộc',
        ];
    }
    public function chunkSize(): int
    {
        return 500;
    }
}
