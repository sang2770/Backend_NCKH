<?php

namespace App\Http\Controllers;
use App\Exports\ConfirmMilitaryExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class ConfirmMilitaryController extends Controller
{
    public function Confirms(){
        // $tks = DB::select('select * from tb_tk_quanly');
        // return view('welcome', ['tks' => $tks]);
        return Excel::download(new ConfirmMilitaryExport, 'ConfirmMilitary.xlsx');
    }

    public function Confirm($id){
        $confirm = new ConfirmMilitaryExport;
        // $confirm = $confirm->collection();
        // return Excel::download($confirm, 'ConfirmMilitary.xlsx');
        return $confirm;
    }
}
