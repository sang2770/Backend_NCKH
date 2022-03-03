<?php

namespace App\Http\Controllers;

use App\Models\Tb_thongbaochinh;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Exception;
use App\Http\Requests\NotificationRequest;
use App\Models\Tb_giay_cn_dangky;
use App\Models\Tb_sinhvien;
use App\Models\Tb_thongbaosv;
use Illuminate\Support\Facades\File;


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
            $notification = Tb_thongbaochinh::select('MaThongBaoChinh', 'TieuDeTB', 'NoiDungTB', 'FileName')->where('MaThongBaoChinh', $id)->get();
            return response()->json(['status' => "Success", 'data' => $notification]);
        }
        else{
            return response()->json(['status' => "Failed"]);
        }
    }
    
    public function countFile()
    {
        $count = 0;
        foreach (File::allFiles(public_path('FileNoti')) as $value) {
            $count++;
        }
        return $count;
    }
    public function create($Input){
        try {
            $filename = "";
            $ThoiGianTB = Carbon::now()->toDateString();
            return [
                'TieuDeTB'          => $Input['TieuDeTB'],
                'NoiDungTB'         => $Input['NoiDungTB'],
                'FileName'          => $filename,
                'ThoiGianTao'        => $ThoiGianTB
            ];
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function createFile($Input){
        try {
            $count = $this->countFile();
            $filename = $Input['file']->getClientOriginalName();
            $filename = $count . "_" . $filename;
            $ThoiGianTB = Carbon::now()->toDateString();
            return [
                'TieuDeTB'          => $Input['TieuDeTB'],
                'NoiDungTB'         => $Input['NoiDungTB'],
                'FileName'          => $filename,
                'ThoiGianTao'        => $ThoiGianTB
            ];
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function StoreNotification(NotificationRequest $request){
        $Notification = $request->validated();
        try {
            if($request->hasFile('file'))
            {
                $Notification = $this->createFile($request->all());
                $count = $this->countFile();
                $filename = $request->file->getClientOriginalName();
                $filename = $count . "_" . $filename;
                $request->file->move(public_path("FileNoti"), $filename);
            }
            else{
                $Notification = $this->create($request->all());
            }
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

    public function Res($Input, $id){
        try {
            $filename = Tb_thongbaochinh::select('FileName')->where('MaThongBaoChinh', $id)->get();
            return [
                'TieuDeTB'          => $Input['TieuDeTB'],
                'NoiDungTB'         => $Input['NoiDungTB'],
                'FileName'          => $filename[0]['FileName'],
            ];
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function UpdateNotification(NotificationRequest $request, $id){
        try {
            if(Tb_thongbaochinh::where('MaThongBaoChinh', $id)->exists()){
                $task = $this->edit($id);
                $input = $this->Res($request->all(), $id);
                $task->fill($input)->save();
                return response()->json(['status' => "Success updated"]);
            }else{
                return response()->json(['status' => "Not Found!"]);
            }
        } catch (Exception $e) {
            return response()->json(['status' => "Failed", 'Err_Message' => $e->getMessage()]);
        }
    }

    //update file
    public function UpdateFile(Request $request, $id){
        try{
            if($request->hasFile('file'))
            {
                $filename = Tb_thongbaochinh::select('FileName')->where('MaThongBaoChinh', $id)->get();
                if($filename[0]['FileName'] != ""){
                    $name = $filename[0]['FileName'];
                }else{
                    $count = $this->countFile();
                    $name = $request->file->getClientOriginalName();
                    $name = $count . "_" . $name;
                }
                $request->file->move(public_path("FileNoti"), $name);
                return response()->json(['status' => "Success", 'data' => $name]);
            }else{
                return response()->json(['status' => "Failed"]);
            }
        }catch(Exception $e){
            return response()->json(['status' => "Failed", 'Err' => $e->getMessage()]);
        }
    }

    //cap nhat filename sau khi uploadfile
    public function ResUpdate($id, $filename){
        try {
            $info = Tb_thongbaochinh::select('*')->where('MaThongBaoChinh', $id)->get();
            return [
                'TieuDeTB'          => $info[0]['TieuDeTB'],
                'NoiDungTB'         => $info[0]['NoiDungTB'],
                'FileName'          => $filename,
            ];
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    
    public function UpdateName($id, $filename){
        try {
            if(Tb_thongbaochinh::where('MaThongBaoChinh', $id)->exists()){
                $task = $this->edit($id);
                $input = $this->ResUpdate($id, $filename);
                $task->fill($input)->save();
                return response()->json(['status' => "Success", 'data' => $filename]);
            }else{
                return response()->json(['status' => "Not Found!"]);
            }
        } catch (Exception $e) {
            return response()->json(['status' => "Failed", 'Err_Message' => 'Dữ liệu đầu vào sai!']);
        }
    }

    //xoa file
    public function DeleteFile($id){
        try{
            $filename = Tb_thongbaochinh::select('FileName')->where('MaThongBaoChinh', $id)->get();
            $path = public_path('FileNoti/' . $filename[0]["FileName"]);
            File::delete($path);
            return response()->json(['status' => "Success"]);
        }catch(Exception $e){
            return response()->json(['status' => "Failed"]);
        }
    }
    //cap nhat ten file sau khi xoa file
    public function ResDelete($id){
        try {
            $info = Tb_thongbaochinh::select('*')->where('MaThongBaoChinh', $id)->get();
            $filename = "";
            return [
                'TieuDeTB'          => $info[0]['TieuDeTB'],
                'NoiDungTB'         => $info[0]['NoiDungTB'],
                'FileName'          => $filename,
            ];
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function UpdateNoti($id){
        try {
            if(Tb_thongbaochinh::where('MaThongBaoChinh', $id)->exists()){
                $task = $this->edit($id);
                $input = $this->ResDelete($id);
                $task->fill($input)->save();
                return response()->json(['status' => "Success"]);
            }else{
                return response()->json(['status' => "Not Found!"]);
            }
        } catch (Exception $e) {
            return response()->json(['status' => "Failed", 'Err_Message' => 'Dữ liệu đầu vào sai!']);
        }
    }

    //xoa thong bao
    public function DestroyNotification($id){
        if(Tb_thongbaochinh::where('MaThongBaoChinh', $id)->exists()){
            $filename = Tb_thongbaochinh::select('FileName')->where('MaThongBaoChinh', '=', $id)->get();
            if($filename != ""){
                $path = public_path('FileNoti/' . $filename[0]["FileName"]);
                File::delete($path);
            }
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
                            ->leftJoin('Tb_giay_cn_dangky', 'Tb_giay_cn_dangky.MaSinhVien', '=', 'Tb_sinhvien.MaSinhVien')
                            ->select('Tb_sinhvien.MaSinhVien', 'Tb_giay_cn_dangky.MaGiayDK', 'Tb_tk_sinhvien.MaTKSV');

        if($request->MaGiayDK){
            $info = $info->where('Tb_giay_cn_dangky.MaGiayDK', '=', null);
        }
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
            return response()->json(['status' => "Success", 'data' => $info]);
        }
        else{
            return response()->json(['status' => "Not Found"]);
        }
    }
}
