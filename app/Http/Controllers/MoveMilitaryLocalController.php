<?php

namespace App\Http\Controllers;

use App\Http\Requests\MoveMilitaryLocalRequest;
use App\Imports\MoveMilitaryLocalImport;
use App\Models\Tb_giay_dc_diaphuong;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Exception;

class MoveMilitaryLocalController extends Controller
{
    //import file
    public function StoreFile(Request $request){
        try {
            Excel::import(new MoveMilitaryLocalImport, $request->file);
            return response()->json(['status' => "Success"]);
        } catch (Exception $e) {
            return response()->json(['status' => "Failed", 'Err_Message' => $e->getMessage()]);
        }
    }

    //thêm từng giấy dc dia phuong
    public function Store(MoveMilitaryLocalRequest $request){
        $validated = $request->validated();
        try {
            $validated = $request->safe()->only('SoGioiThieu', 'NgayCap', 'NgayHH', 'NoiOHienTai', 'NoiChuyenDen', 'LyDo', 'BanChiHuy', 'MaGiayDK');
            Tb_giay_dc_diaphuong::insert($validated);
            return response()->json(['status' => "Success", 'data' => ["ThongTin" => $validated]]);
        } catch (Exception $e) {
            return response()->json(['status' => "Failed", 'Err_Message' => $e->getMessage()]);
        }
    }

}
