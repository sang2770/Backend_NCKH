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
            // $LanCap = tb_yeucau::select('MaSinhVien', DB::raw('count(*) as LanXinCap'))
            //     ->groupBy('MaSinhVien');
            $ListRequest = Tb_sinhvien::join('tb_lop', 'tb_sinhvien.MaLop', '=', 'tb_lop.MaLop')
                ->join('tb_khoa', 'tb_lop.MaKhoa', '=', 'tb_khoa.MaKhoa')->join("tb_yeucau", "tb_yeucau.MaSinhVien", '=', 'tb_sinhvien.MaSinhVien')
                // ->joinSub($LanCap, "Res", function ($join) {
                //     $join->on('tb_sinhvien.MaSinhVien', '=', 'Res.MaSinhVien');
                // })
                ->filter($request)
                ->paginate($limit, [
                    'tb_sinhvien.MaSinhVien',
                    "HoTen",
                    'NgayYeuCau',
                    'LanXinCap',
                    'TrangThaiXuLy',
                    'MaYeuCau'
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

    public function confirmIndex(Request $request)
    {
        $ListStudentID = json_decode($request->MSV);
        $Date = date('y-m-d H-i');
        try {
            Tb_yeucau::where('MaYeuCau', '>', 0)->update(['TrangThaiXuLy' => 'Đã xử lý', "NgayXuLy" => $Date]);
            return response()->json(['status' => "Success"]);
        } catch (Exception $e) {
            return response()->json(['status' => "Failed", 'Err_Message' => $e->getMessage()]);
        }
    }
    public function confirm(Request $request, $id)
    {
        $Date = date('y-m-d H-i');
        try {
            Tb_yeucau::where('MaSinhVien', $id)->where('MaYeuCau', $request->MaYeuCau)
                ->update(['TrangThaiXuLy' => 'Đã xử lý', "NgayXuLy" => $Date]);
            return response()->json(['status' => "Success"]);
        } catch (Exception $e) {
            return response()->json(['status' => "Failed", 'Err_Message' => $e->getMessage()]);
        }
    }
}
