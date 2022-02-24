<?php

namespace App\Http\Controllers;

use App\Models\Tb_thongbaochinh;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Exception;
use App\Http\Requests\NotificationRequest;
use App\Models\Tb_sinhvien;
use App\Models\Tb_thongbaosv;

class NotificationController extends Controller
{
    public function IndexHeader(Request $request){
        $limit = $request->query('limit');
        $page = $request->query('page');
        $count = Tb_thongbaochinh::select('MaThongBaoChinh')->count();
        if($count > 0){
            $notification = Tb_thongbaochinh::orderBy('MaThongBaoChinh', 'DESC')->paginate($perPage = $limit, $columns = ['MaThongBaoChinh', 'TieuDeTB'], $pageName = 'page', $page)->toArray();
            return response()->json(['status' => "Success", 'data' => $notification["data"], 'pagination' => [
                "page" => $notification['current_page'],
                "first_page_url"    => $notification['first_page_url'],
                "next_page_url"     => $notification['next_page_url'],
                "TotalPage"         => $notification['last_page']
            ]]);
        }
        else{
            return response()->json(['status' => "Failed"]);
        }
    }

    public function show($id){
        if(Tb_thongbaochinh::where('MaThongBaoChinh', $id)->exists()){
            $notification = Tb_thongbaochinh::select('MaThongBaoChinh', 'TieuDeTB', 'NoiDungTB')->where('MaThongBaoChinh', $id)->get();
            return response()->json(['status' => "Success", 'data' => $notification]);
        }
        else{
            return response()->json(['status' => "Failed"]);
        }
    }

    public function create($Input){
        try {
            $ThoiGianTB = Carbon::now()->toDateString();
            return [
                'TieuDeTB'          => $Input['TieuDeTB'],
                'NoiDungTB'         => $Input['NoiDungTB'],
                'ThoiGianTao'        => $ThoiGianTB
            ];
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function StoreNotification(NotificationRequest $request){
        $Notification = $request->validated();
        try {
            $Notification = $this->create($request->all());
            Tb_thongbaochinh::insert($Notification);
            return response()->json(['status' => "Success", 'data' => ["ThongBao" => $Notification]]);
        } catch (Exception $e) {
            return response()->json(['status' => "Failed", 'Err_Message' => $e->getMessage()]);
        }
    }

    public function edit($id){
        $edit = Tb_thongbaochinh::findOrFail($id);
        return $edit;
    }

    public function UpdateNotification(NotificationRequest $request, $id){
        if(Tb_thongbaochinh::where('MaThongBaoChinh', $id)->exists()){
            $task = $this->edit($id);
            $input = $request->all();
            $task->fill($input)->save();
            return response()->json(['status' => "Success updated", 'data' => $task->fill($input)]);
        }
        else{
            return response()->json(['status' => "Not Found!"]);
        }
    }

    public function DestroyNotification($id){
        if(Tb_thongbaochinh::where('MaThongBaoChinh', $id)->exists()){
            Tb_thongbaochinh::where('MaThongBaoChinh', $id)->delete();
            return response()->json(['status' => "Success deleted"]);
        }
        else{
            return response()->json(['status' => "Not Found!"]);
        }
    }

    public function SentNotificationStudent(Request $request){
        $info = Tb_sinhvien::join('Tb_lop', 'Tb_lop.MaLop', '=', 'Tb_sinhvien.MaLop')
                            ->join('Tb_khoa','Tb_khoa.MaKhoa', '=', 'Tb_lop.MaKhoa')
                            ->join('Tb_tk_sinhvien', 'Tb_tk_sinhvien.MaSinhVien', '=', 'Tb_sinhvien.MaSinhVien')
                            ->select('Tb_tk_sinhvien.MaTKSV', 'Tb_sinhvien.MaSinhVien');

        if($request->MaSinhVien){
            $info = $info->where('Tb_sinhvien.MaSinhVien', '=', $request->MaSinhVien);
        }
        if($request->TenLop){
            $info = $info->where('Tb_lop.TenLop', '=', $request->TenLop);
        }
        if($request->TenKhoa){
            $info = $info->where('Tb_khoa.TenKhoa', '=', $request->TenKhoa);
        }
        if($request->Khoas){
            $info = $info->where('Tb_lop.Khoas', '=', $request->Khoas);
        }

        $count = $info->count();
        $info = $info->get();

        if($count!=0 && Tb_thongbaochinh::where('MaThongBaoChinh', '=', $request->MaThongBaoChinh)->exists()){
            foreach($info as $i){
                if(Tb_thongbaosv::where('MaTKSV', '=', $i["MaTKSV"])->where('MaThongBaoChinh', '=', $request->MaThongBaoChinh)->doesntExist()){
                    Tb_thongbaosv::insert([
                        'ThoiGianTB'        => Carbon::now()->toDateString(),
                        'MaTKSV'            => $i["MaTKSV"],
                        'MaThongBaoChinh'   => $request->MaThongBaoChinh,
                    ]);
                }
            }
            return response()->json(['status' => "Success"]);
        }
        else{
            return response()->json(['status' => "Not Found"]);
        }
    }
}
