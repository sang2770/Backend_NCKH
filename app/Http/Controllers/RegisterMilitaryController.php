<?php

namespace App\Http\Controllers;
use App\Models\Tb_giay_cn_dangky;
use App\Imports\RegisterMilitaryImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Exception;
use App\Http\Requests\RegisterMilitaryRequest;

class RegisterMilitaryController extends Controller
{
    // Import file
    public function StoreFile(Request $request){
        try {
            Excel::import(new RegisterMilitaryImport, $request->file);
            return response()->json(['status' => "Success"]);
        } catch (Exception $e) {
            return response()->json(['status' => "Failed", 'err' => $e->getMessage()]);
        }
    }

    public function Store(RegisterMilitaryRequest $request){
        $validated = $request->validated();
        try {
            $validated = $request->safe()->only('SoDangKy', 'NgayDangKy', 'NoiDangKy', 'DiaChiThuongTru', 'NgayNop', 'MaSinhVien');
            Tb_giay_cn_dangky::insert($validated);
            return response()->json(['status' => "Success", 'data' => ["ThongTinDangKy" => $validated]]);
        } catch (Exception $e) {
            return response()->json(['status' => "Failed", 'Err_Message' => $e->getMessage()]);
        }
    }
}
