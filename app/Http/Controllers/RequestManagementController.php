<?php

namespace App\Http\Controllers;

use App\Models\Tb_sinhvien;
use App\Models\Tb_yeucau;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RequestManagementController extends Controller
{
    /**
     * Lấy danh sách yêu cầu cần xác nhận
     */
    public function index(Request $request)
    {
        // MaSv, TenSV, NgayYeuCau, LanXinCap,XuLy

        try {
            $limit = $request->limit;
            $page = $request->page;
            $LanCap = DB::table('Tb_yeucau')
                ->where('NgayXuLy', "=", null)
                ->select('MaSinhVien', DB::raw('count(*) as LanXinCap'))
                ->groupBy('MaSinhVien');
            $ListRequest = Tb_sinhvien::join("Tb_yeucau", "Tb_yeucau.MaSinhVien", '=', 'Tb_sinhvien.MaSinhVien')
                ->joinSub($LanCap, "Res", function ($join) {
                    $join->on('Tb_sinhvien.MaSinhVien', '=', 'Res.MaSinhVien');
                })
                ->filter($request)
                ->paginate($limit, [
                    'Tb_sinhvien.MaSinhVien',
                    "HoTen",
                    'NgayYeuCau',
                    'LanXinCap'
                ], 'page', $page)->toArray();

            return response()->json(['status' => "Success", 'data' => $ListRequest['data'], 'pagination' => [
                "page" => $ListRequest['current_page'],
                "limit" => $limit,
                "TotalPage" => $ListRequest['last_page']
            ]]);
        } catch (Exception $e) {
            return response()->json(['status' => "Failed", 'Err_Message' => $e->getMessage()]);
        }
    }

    public function confirm(Request $request)
    {
        $ListStudentID = $request->MSV;
        $Date = date('y-m-d H-i');
        try {
            foreach ($ListStudentID as $Id) {
                Tb_yeucau::where('MaSinhVien', $Id)
                    ->update(['TrangThaiXuLy' => 'Đã xử lý', "NgayXuLy" => $Date]);
            }
            return response()->json(['status' => "Success"]);
        } catch (Exception $e) {
            return response()->json(['status' => "Failed", 'Err_Message' => $e->getMessage()]);
        }
    }
}