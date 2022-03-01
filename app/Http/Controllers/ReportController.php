<?php

namespace App\Http\Controllers;
use App\Models\Tb_Err_importStudent;
use App\Models\Tb_lichsu;
use App\Models\Tb_lop;
use App\Models\Tb_sinhvien;
use Carbon\Carbon;
use DateTime;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpWord\TemplateProcessor;

class ReportController extends Controller
{
    //Thống kê biến động
    private function LogicFluctuations($request)
    {
        $student=Tb_sinhvien::filter($request);
        $Learning=$student->select(
            DB::raw("
            sum(case when month(NgayQuanLy)<=1 then 1 else 0  END) as '1',
            sum(case when month(NgayQuanLy)<=2 then 1 else 0  END) as '2',
            sum(case when month(NgayQuanLy)<=3 then 1 else 0  END) as '3',
            sum(case when month(NgayQuanLy)<=4 then 1 else 0  END) as '4',
            sum(case when month(NgayQuanLy)<=5 then 1 else 0  END) as '5',
            sum(case when month(NgayQuanLy)<=6 then 1 else 0  END) as '6',
            sum(case when month(NgayQuanLy)<=7 then 1 else 0  END) as '7',
            sum(case when month(NgayQuanLy)<=8 then 1 else 0  END) as '8',
            sum(case when month(NgayQuanLy)<=9 then 1 else 0  END) as '9',
            sum(case when month(NgayQuanLy)<=10 then 1 else 0  END) as '10',
            sum(case when month(NgayQuanLy)<=11 then 1 else 0  END) as '11',
            sum(case when month(NgayQuanLy)<=12 then 1 else 0  END) as '12',
            count(tb_sinhvien.MaSinhVien) as Tong
            ")
        )->where('TinhTrangSinhVien', 'like', "%Đang học%")->first()->toArray();
        $student=Tb_sinhvien::filter($request);
        $Out=$student->select(
            DB::raw("
            sum(case when month(NgayKetThuc)=1 then 1 else 0  END) as '1',
            sum(case when month(NgayKetThuc)=2 then 1 else 0  END) as '2',
            sum(case when month(NgayKetThuc)=3 then 1 else 0  END) as '3',
            sum(case when month(NgayKetThuc)=4 then 1 else 0  END) as '4',
            sum(case when month(NgayKetThuc)=5 then 1 else 0  END) as '5',
            sum(case when month(NgayKetThuc)=6 then 1 else 0  END) as '6',
            sum(case when month(NgayKetThuc)=7 then 1 else 0  END) as '7',
            sum(case when month(NgayKetThuc)=8 then 1 else 0  END) as '8',
            sum(case when month(NgayKetThuc)=9 then 1 else 0  END) as '9',
            sum(case when month(NgayKetThuc)=10 then 1 else 0  END) as '10',
            sum(case when month(NgayKetThuc)=11 then 1 else 0  END) as '11',
            sum(case when month(NgayKetThuc)=12 then 1 else 0  END) as '12',
            count(tb_sinhvien.MaSinhVien) as Tong
            ")
        )->where('TinhTrangSinhVien', 'not like', "%Đang học%")->first()->toArray();
        // var_dump(Tb_sinhvien::where('TinhTrangSinhVien', 'not like', "%Đang học%")->first()->toArray());
        $chart=[];
        if($Learning['Tong']!=0 || $Out['Tong']!=0)
        {
        foreach ($Learning as $key => $value) {
            if($key!='Tong')
            {
            $chart[]=[$value, $Out[$key]?$Out[$key]:0];
            }
        }
        }
        return [$Learning['Tong'], $Out['Tong'], $chart];
    }

    public function ExportFluctuations(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'Nam' => 'required',
        ]);
        if ($validator->fails()) {
            // Bad Request
            return response()->json(['status' => "Failed", 'Err_Message' => 'Thiếu dữ liệu đầu vào']);
        }
       try {
        $result=$this->LogicFluctuations($request);
        return response()->json([
            'status'=>"Success",
            'data'=>[
                "Total_Learning"=>$result[0],
                "Total_Out"=>$result[1],
                "Chart"=>$result[2]
            ]
            ]);
       } catch (Exception $e) {
        return response()->json(['status' => "Failed", 'Err_Message' => $e->getMessage()]);
           
       }
    }
    // Thống kê cập nhật
    private function LogicUpdateReport($request)
    {
        $list = Tb_lichsu::where('tb_lichsu.MaSinhVien', $request->MaSinhVien);
            
            if($list->count()==0)
            {
               return response()->json(['status' => "Failed", 'Err_Message' =>"Not found!"]);
            }
            if($request->Ngay)
            {
                $list =$list->whereDate('ThoiGian', $request->Ngay);
            }
            if($request->Thang)
            {
                $list =$list->whereMonth('ThoiGian', $request->Thang);

            }
            if($request->Nam)
            {
                $list =$list->whereYear('ThoiGian', $request->Nam);
            }
            $list=$list
                ->join('tb_tk_quanly', 'tb_tk_quanly.MaTK', '=', 'tb_lichsu.MaTK')
                ->join('tb_sinhvien', 'tb_sinhvien.MaSinhVien', '=', 'tb_lichsu.MaSinhVien')
                ->select('NoiDung', 'TenDangNhap', 'ThoiGian', 'tb_lichsu.MaSinhVien');
            return $list;           
    }
    public function ExportUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'Nam' => 'required',
        ]);
        if ($validator->fails()) {
            // Bad Request
            return response()->json(['status' => "Failed", 'Err_Message' => 'Thiếu dữ liệu đầu vào']);
        }
        $Tranfer = [
            'MaSinhVien' => "Mã sinh viên",
            'HoTen' => 'Họ và tên',
            'NgaySinh' => 'Ngày sinh',
            'NoiSinh' => 'Nơi sinh',
            'GioiTinh' => 'Giới tính',
            'DanToc' => 'Dân tộc',
            'TonGiao' => 'Tôn giáo',
            'QuocTich' => 'Quốc tịch',
            'SoCMTND' => 'Số Chứng minh nhân dân',
            'NgayCapCMTND' => 'Ngày cấp CMTND',
            'NoiCapCMTND' => 'Nơi cấp CMTND',
            'DiaChiBaoTin' => 'Địa chỉ báo tin',
            'SDT' => 'Số điện thoại',
            'Email' => 'Email',
            'HoKhauTinh' => 'Hộ khẩu tỉnh',
            'HoKhauHuyen' => 'Hộ khẩu huyện',
            'HoKhauXaPhuong' => 'Hộ khẩu xã/phường',
            'TinhTrangSinhVien' => 'Tình trạng sinh viên',
            'HeDaoTao' => 'Hệ đào tạo',
            'TenLop' => 'Tên lớp'
        ];
        try {
            $limit = $request->query('limit');
            $page = $request->query('page');
            $list=$this->LogicUpdateReport($request);
            $list=$list->paginate($limit, [
                    '*'
            ], 'page', $page)->toArray();
                $result=[];
            foreach ($list['data'] as  $item) {
                $Fields = explode(";", $item['NoiDung']);
                unset($Fields[count($Fields) - 1]);
                $Context = [];
                foreach ($Fields as $value) {
                    $content = explode(":", $value);
                    if ($content[0] === "MaLop") {
                        $Lop = Tb_lop::find($content[1])->TenLop;
                        $Context["Tên lớp"] = $Lop;
                    } else {
                        $Context[$Tranfer[$content[0]]] = $content[1];
                    }
                }
                $item['NoiDung'] = $Context;
                $item['ThoiGian']=Carbon::parse($item['ThoiGian'])->format('Y-m-d h:m:s ');
                $result[]=$item;
            }
            return response()->json([
                'status'=>"Success",
                'data'=>[
                    "Total_Update"=>count($result),
                    "Details"=>$result
                ],'pagination' => [
                    "page" => $list['current_page'],
                    "limit" => $limit,
                    "TotalPage" => $list['last_page']
                ]
                ]);
        } catch (Exception $e) {
            return response()->json(['status' => "Failed", 'Err_Message' => $e->getMessage()]);
        }
    }
    // Thống kê Import
    private function LogicExportImport($request)
    {
        $Err=Tb_Err_importStudent::select("*");
            if($request->Nam)
            {
                $Err=$Err->whereYear("ThoiGian", $request->Nam);
            }
            if($request->Ngay)
            {
                $Err =$Err->whereDate('ThoiGian', $request->Ngay);
            }
            if($request->Thang)
            {
                $Err =$Err->whereMonth('ThoiGian', $request->Thang);

            }
            $HistoryCount=$Err->select(DB::raw("
            sum(case when TrangThai=N'Success' then 1 else 0  END) as 'Total_Success',
            sum(case when TrangThai=N'Failed' then 1 else 0  END) as 'Total_Failed'
            "
            ))->first()->toArray();
            
            $History=Tb_Err_importStudent::join('tb_tk_quanly', 'tb_tk_quanly.MaTK', '=', 'tb_ErrImportStudent.MaTK')
            ->select('TenDangNhap', 'NoiDung', 'ThoiGian', 'TrangThai');
            return [$HistoryCount, $History];
    }
    public function ExportImport(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'Nam' => 'required',
        ]);
        if ($validator->fails()) {
            // Bad Request
            return response()->json(['status' => "Failed", 'Err_Message' => 'Thiếu dữ liệu đầu vào']);
        }
        try {
            $limit = $request->query('limit');
            $page = $request->query('page');
            $ResultLogic=$this->LogicExportImport($request);
            $History=$ResultLogic[1]->paginate($limit, [
                '*'
            ], 'page', $page)->toArray();
            return response()->json([
                'status'=>"Success",
                'data'=>[
                    "Total_Success"=>$ResultLogic[0]["Total_Success"],
                    "Total_Failed"=>$ResultLogic[0]["Total_Failed"],
                    "Details"=>$History['data']
                ],'pagination' => [
                    "page" => $History['current_page'],
                    "limit" => $limit,
                    "TotalPage" => $History['last_page']
                ]
                ]);
            } catch (Exception $e) {
                return response()->json(['status' => "Failed", 'Err_Message' => $e->getMessage()]);
                
           }
    }
    public function ReportFluctuations(Request $request){
        $validator = Validator::make($request->all(), [
            'Nam' => 'required',
        ]);
        if ($validator->fails()) {
            // Bad Request
            return response()->json(['status' => "Failed", 'Err_Message' => 'Thiếu dữ liệu đầu vào']);
        }
        try {
        $templateProcessor = new TemplateProcessor('TemplateReport/FluctuationsReport.docx');
        $date=Carbon::now();
        $result=$this->LogicFluctuations($request);
        $Export=[
            'Ngay'=>$date->day,
            'Thang'=>$date->month,
            'Nam'=>$date->year,
            'NamTk'=>$request->Nam,
            'Lop'=>$request->Lop,
            'Khoa'=>$request->Khoa,
            'Khoas'=>$request->Khoas,
            "Total_Learning"=>$result[0],
            "Total_Out"=>$result[1],  
        ];
        foreach ($result[2] as $key=> $value) {
            $Export['I'.($key+1)]=$value[0];
            $Export['O'.($key+1)]=$value[1];
        }
        // var_dump($Export);
        $templateProcessor->cloneBlock('block_name', 0, true, false, [$Export]);
        $filename = "Report";
        $templateProcessor->saveAs($filename . '.docx');
        return response()->download($filename . '.docx')->deleteFileAfterSend(true);
        } catch (Exception $e) {
            return response()->json(['status' => "Failed", 'Err_Message' => $e->getMessage()]);
        }
    }
    public function ReportUpdate(Request $request){
        $validator = Validator::make($request->all(), [
            'Nam' => 'required',
        ]);
        if ($validator->fails()) {
            // Bad Request
            return response()->json(['status' => "Failed", 'Err_Message' => 'Thiếu dữ liệu đầu vào']);
        }
        try {
        $templateProcessor = new TemplateProcessor('TemplateReport/UpdateReport.docx');
        $date=Carbon::now();
        $ResultLogic=$this->LogicUpdateReport($request);
        $ListUpdate=$ResultLogic->get()->toArray();
        $Export=[
            'Ngay'=>$date->day,
            'Thang'=>$date->month,
            'Nam'=>$date->year,
            'NamTk'=>$request->Nam?"Năm ".$request->Nam:"",
            'ThangTK'=>$request->Thang?"Tháng ".$request->Thang:"",
            'NgayTK'=>$request->Ngay?"Ngày ".$request->Ngay:"",
            'Total_Update'=>count($ListUpdate),
            'MaSinhVien'=>$request->MaSinhVien
        ];
        $templateProcessor->cloneBlock('block_name', 0, true, false, [$Export]);
        $Details=[];
        foreach ($ListUpdate as $value) {
            $date=Carbon::parse($value['ThoiGian'])->format('Y/m/d h:m:s');
            $Details[]=['ThoiGian'=>$date, 'NoiDung'=>$value['NoiDung'], 'User'=>$value['TenDangNhap']];
        }
        $templateProcessor->cloneRowAndSetValues('ThoiGian', $Details);
        $filename = "Report";
        $templateProcessor->saveAs($filename . '.docx');
        return response()->download($filename . '.docx')->deleteFileAfterSend(true);
        } catch (Exception $e) {
            return response()->json(['status' => "Failed", 'Err_Message' => $e->getMessage()]);
        }
    }
    public function ReportImport(Request $request){
        $validator = Validator::make($request->all(), [
            'Nam' => 'required',
        ]);
        if ($validator->fails()) {
            // Bad Request
            return response()->json(['status' => "Failed", 'Err_Message' => 'Thiếu dữ liệu đầu vào']);
        }
        try {
        $templateProcessor = new TemplateProcessor('TemplateReport/ImportReport.docx');
        $date=Carbon::now();
        $ResultLogic=$this->LogicExportImport($request);
        $HistoryCount=$ResultLogic[0];
        $History=$ResultLogic[1];

        $Export=[
            'Ngay'=>$date->day,
            'Thang'=>$date->month,
            'Nam'=>$date->year,
            'NamTk'=>$request->Nam,
            'ThangTK'=>$request->Thang?"Tháng ".$request->Thang:"",
            'NgayTK'=>$request->Ngay?"Ngày ".$request->Ngay:"",
            "Total_Update"=>($HistoryCount['Total_Success']+$HistoryCount['Total_Failed']),
            "Total_Success"=>$HistoryCount['Total_Success'],
            "Total_Failed"=>$HistoryCount['Total_Failed'],  
        ];
        $templateProcessor->cloneBlock('block_name', 0, true, false, [$Export]);
        $Details=[];
        $History=$History->get()->toArray();
        foreach ($History as $value) {
            $date=Carbon::parse($value['ThoiGian'])->format('Y/m/d h:m:s');
            $Details[]=['ThoiGian'=>$date, 'NoiDung'=>$value['NoiDung'], 'User'=>$value['TenDangNhap'], 'Status'=>$value['TrangThai']];
        }
        $templateProcessor->cloneRowAndSetValues('ThoiGian', $Details);
        $filename = "Report";
        $templateProcessor->saveAs($filename . '.docx');
        return response()->download($filename . '.docx')->deleteFileAfterSend(true);
        } catch (Exception $e) {
            return response()->json(['status' => "Failed", 'Err_Message' => $e->getMessage()]);
        }
    }
    
    


}
