<?php

namespace App\Http\Controllers;
use App\Exports\MoveMilitaryExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class MoveMilitaryController extends Controller
{
    public function Moves(){
        return Excel::download(new MoveMilitaryExport(2), 'MoveMilitary.xlsx');
    }

    public function Move($id){
        $move = new MoveMilitaryExport($id);
        $move = $move->collection();
        return $move;
    }
}
