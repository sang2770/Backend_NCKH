<?php

namespace App\Imports;

use App\Models\Tb_khoa;
use App\Models\Tb_Lop;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
class ClassImport implements ToModel, WithHeadingRow, WithChunkReading, SkipsEmptyRows, WithValidation
{
    use Importable, SkipsFailures;
    protected $rowNum = 1;
    public $Err = [];
    public function model(array $row)
    {
        ++$this->rowNum;
        $TenKhoa = $row['ten_khoa'];
        $MaKhoa = Tb_khoa::where('TenKhoa', $TenKhoa)->value('MaKhoa');

        if (!$MaKhoa) {
            $error = ['err' => "Không tồn tại Tên Khoa!", "row" => $this->rowNum];
            $this->Err[] = $error;
            return null;
        }
        if (empty($row['tt'])) {
            return null;
        }
        var_dump([
            'TenLop' => $row['ten_lop'],
            'Khoas' => $row['khoa'],
            'MaKhoa' => $MaKhoa,
        ]);
        return [
            new Tb_Lop([
                'TenLop' => $row['ten_lop'],
                'Khoas' => $row['khoa'],
                'MaKhoa' => $MaKhoa,
            ])
        ];
    }
    public function  rules(): array
    {
        return [
            '*.ten_lop' => 'required',
            '*.khoa' => 'required',
            '*.ten_khoa' => 'required'

        ];
    }
    public function chunkSize(): int
    {
        return 500;
    }
}
