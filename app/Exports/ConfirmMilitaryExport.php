<?php

namespace App\Exports;

use App\Models\Tb_giay_xn_truong;
use App\Models\Tb_tk_quanly;

// use App\Invoice;
// use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ConfirmMilitaryExport implements FromView
{
    public function view(): View
    {
        return view('Tb_tk_ql', [
            'TaiKhoans' => Tb_tk_quanly::all()
        ]);
    }
}

// class ConfirmMilitaryExport implements FromCollection
// {
//     public function __construct()
//     {

//     }

//     public function __construct1(int $id)
//     {
//         $this->MaTK = $id;
//     }
//     /**
//     * @return \Illuminate\Support\Collection
//     */
//     public function collection()
//     {
//         return Tb_tk_quanly::all();
//     }
// }

class InvoicesExport implements FromQuery
{
    use Exportable;

    public function __construct(int $year)
    {
        $this->year = $year;
    }

    public function query()
    {
        return Tb_tk_quanly::query()->whereYear('created_at', $this->year);
    }
}