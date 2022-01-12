<?php

namespace App\Exports;

use App\Models\Tb_giay_dc_truong;
use Maatwebsite\Excel\Concerns\FromCollection;

class MoveMilitaryExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function __construct(int $id)
    {
        $this->MaTK = $id;
    }

    public function collection()
    {
        // return Tb_tk_quanly::all()->where('MaTK', $this->MaTK);
        return Tb_giay_dc_truong::all();
    }
}
