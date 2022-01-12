<?php

namespace App\Http\Controllers;

use App\Models\Tb_thongbaochinh;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Exception;
use App\Http\Requests\NotificationRequest;

class NotificationController extends Controller
{
    public function IndexHeader(){
        $count = Tb_thongbaochinh::select('MaThongBaoChinh')->count();
        if($count > 0){
            $notification = Tb_thongbaochinh::select('TieuDeTB')->get();
            return response()->json(['status' => "Success", 'data' => $notification]);
        }
        else{
            return response()->json(['status' => "Failed"]);
        }
    }

    public function show($id){
        $count = Tb_thongbaochinh::select('MaThongBaoChinh')->where('MaThongBaoChinh', $id)->count();
        if($count > 0){
            $notification = Tb_thongbaochinh::select('TieuDeTB', 'NoiDungTB')->where('MaThongBaoChinh', $id)->get();
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
                'ThoiGianTB'        => $ThoiGianTB
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
        $count = Tb_thongbaochinh::where('MaThongBaoChinh', $id)->count();
        if($count > 0){
            $task = $this->edit($id);
            $input = $request->all();
            $task->fill($input)->save();
            return response()->json(['status' => "Success updated"]);
        }
        else{
            return response()->json(['status' => "Failed"]);
        }
    }

    public function DestroyNotification($id){
        $count = Tb_thongbaochinh::where('MaThongBaoChinh', $id)->count();
        if($count > 0){
            Tb_thongbaochinh::where('MaThongBaoChinh', $id)->delete();
            return response()->json(['status' => "Success deleted"]);
        }
        else{
            return response()->json(['status' => "Failed"]);
        }
    }

    public function SentNotificationStudent(){
        return "SentNotificationStudent";
    }
}
