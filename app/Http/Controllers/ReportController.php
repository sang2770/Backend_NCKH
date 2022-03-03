<?php

namespace App\Http\Controllers;
use App\Models\Tb_Err_importStudent;
use App\Models\Tb_giay_xn_truong;
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
        $student=Tb_sinhvien::join('Tb_lop', 'Tb_sinhvien.MaLop', '=', 'Tb_lop.MaLop')
        ->join('Tb_khoa', 'Tb_lop.MaKhoa', '=', 'Tb_khoa.MaKhoa')
        ->filter($request)->get()->toArray();
        $Month=array(1,2,3,4,5,6,7,8,9,10,11,12);
        $Learning=[];
        $Learning['Tong']=0;
        foreach ($student as $key=>$value) {
            $time=Carbon::parse($value['NgayQuanLy']);
            for($i=0;$i<count($Month);$i++) {
                $month=$Month[$i];
                if($time->month <= $month  && !$value["NgayKetThuc"] 
                || $value["NgayKetThuc"] && Carbon::parse($value['NgayKetThuc'])->month >= $month)
                {
                    $Learning["$month"] = isset($Learning["$month"])?($Learning["$month"]+1):1; 
                }
                else
                {
                    $Learning["$month"]=0;
                }
            }
        }
        $Learning['Tong']=max($Learning);
        $student=Tb_sinhvien::join('Tb_lop', 'Tb_sinhvien.MaLop', '=', 'Tb_lop.MaLop')
        ->join('Tb_khoa', 'Tb_lop.MaKhoa', '=', 'Tb_khoa.MaKhoa')
        ->filter($request)->where('TinhTrangSinhVien', 'not like', "%Đang học%")->get()->toArray();
        $Out=[];
        $Out['Tong']=0;
        foreach ($student as $key=>$value) {
            $time=Carbon::parse($value['NgayKetThuc']);
            for($i=0;$i<count($Month);$i++) {
                $month=$Month[$i];
                if($time->month == $month)
                {
                    $Out["$month"] = isset($Out["$month"])?($Out["$month"]+1):1; 
                    $Out['Tong']+=1;     
                }else{
                    $Out["$month"]=0;
                }
            }
        }
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
        $list = Tb_lichsu::where('Tb_lichsu.MaSinhVien', $request->MaSinhVien);
            
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
                ->join('Tb_tk_quanly', 'Tb_tk_quanly.MaTK', '=', 'Tb_lichsu.MaTK')
                ->join('tb_sinhvien', 'tb_sinhvien.MaSinhVien', '=', 'Tb_lichsu.MaSinhVien')
                ->select('NoiDung', 'TenDangNhap', 'ThoiGian', 'Tb_lichsu.MaSinhVien');
            return $list;           
    }
    public function ExportUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'Nam' => 'required',
            'MaSinhVien'=>'required'
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
            $HistoryCount=$Err->get(['TrangThai'])->toArray();
            $Total_Success=0;
            $Total_Failed=0;
            foreach ($HistoryCount as $key => $value) {
                if($value['TrangThai']=="Success")
                {
                    $Total_Success+=1;
                }else if($value['TrangThai']=="Failed")
                {
                    $Total_Failed+=1;
                }
            }
            $History=Tb_Err_importStudent::join('Tb_tk_quanly', 'Tb_tk_quanly.MaTK', '=', 'tb_ErrImportStudent.MaTK')
            ->select('TenDangNhap', 'NoiDung', 'ThoiGian', 'TrangThai');
            return [[$Total_Success, $Total_Failed], $History];
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
                    "Total_Success"=>$ResultLogic[0][0],
                    "Total_Failed"=>$ResultLogic[0][1],
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
            "Total_Update"=>($HistoryCount[0]+$HistoryCount[1]),
            "Total_Success"=>$HistoryCount[0],
            "Total_Failed"=>$HistoryCount[1],  
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
    
    ///thong ke tinh trang cap phat giay gioi thieu di chuyen tu truong
    private function CreateReportMove($request)
    {
        $student=Tb_sinhvien::join('Tb_giay_cn_dangky', 'Tb_giay_cn_dangky.MaSinhVien', '=', 'Tb_sinhvien.MaSinhVien')
        ->join('Tb_giay_dc_truong', 'Tb_giay_dc_truong.MaGiayDK', '=', 'Tb_giay_cn_dangky.MaGiayDK')
        ->join('Tb_lop', 'Tb_lop.MaLop', '=', 'Tb_sinhvien.MaLop')
        ->filter($request);
        $Learning=$student->select(
            DB::raw("
            sum(case when month(NgayCap)=1 then 1 else 0  END) as '1',
            sum(case when month(NgayCap)=2 then 1 else 0  END) as '2',
            sum(case when month(NgayCap)=3 then 1 else 0  END) as '3',
            sum(case when month(NgayCap)=4 then 1 else 0  END) as '4',
            sum(case when month(NgayCap)=5 then 1 else 0  END) as '5',
            sum(case when month(NgayCap)=6 then 1 else 0  END) as '6',
            sum(case when month(NgayCap)=7 then 1 else 0  END) as '7',
            sum(case when month(NgayCap)=8 then 1 else 0  END) as '8',
            sum(case when month(NgayCap)=9 then 1 else 0  END) as '9',
            sum(case when month(NgayCap)=10 then 1 else 0  END) as '10',
            sum(case when month(NgayCap)=11 then 1 else 0  END) as '11',
            sum(case when month(NgayCap)=12 then 1 else 0  END) as '12',
            count(Tb_giay_dc_truong.MaGiayDC_Truong) as Tong
            ")
        )->where('TinhTrangSinhVien', 'like', "%Đã tốt nghiệp%");

        if($request->Khoas){
            $Learning = $Learning->where('Tb_lop.Khoas', '=', $request->Khoas);
        }

        $Learning = $Learning->first()->toArray();
        $studentOut=Tb_sinhvien::join('Tb_giay_cn_dangky', 'Tb_giay_cn_dangky.MaSinhVien', '=', 'Tb_sinhvien.MaSinhVien')
        ->join('Tb_giay_dc_truong', 'Tb_giay_dc_truong.MaGiayDK', '=', 'Tb_giay_cn_dangky.MaGiayDK')
        ->join('Tb_lop', 'Tb_lop.MaLop', '=', 'Tb_sinhvien.MaLop')
        ->filter($request);
        $Out=$studentOut->select(
            DB::raw("
            sum(case when month(NgayCap)=1 then 1 else 0  END) as '1',
            sum(case when month(NgayCap)=2 then 1 else 0  END) as '2',
            sum(case when month(NgayCap)=3 then 1 else 0  END) as '3',
            sum(case when month(NgayCap)=4 then 1 else 0  END) as '4',
            sum(case when month(NgayCap)=5 then 1 else 0  END) as '5',
            sum(case when month(NgayCap)=6 then 1 else 0  END) as '6',
            sum(case when month(NgayCap)=7 then 1 else 0  END) as '7',
            sum(case when month(NgayCap)=8 then 1 else 0  END) as '8',
            sum(case when month(NgayCap)=9 then 1 else 0  END) as '9',
            sum(case when month(NgayCap)=10 then 1 else 0  END) as '10',
            sum(case when month(NgayCap)=11 then 1 else 0  END) as '11',
            sum(case when month(NgayCap)=12 then 1 else 0  END) as '12',
            count(Tb_giay_dc_truong.MaGiayDC_Truong) as Tong
            ")
        )->where('TinhTrangSinhVien', 'like', "%Đã thôi học%");
        
        if($request->Khoas){
            $Out = $Out->where('Tb_lop.Khoas', '=', $request->Khoas);
        }

        $Out = $Out->first()->toArray();

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

    public function ReportMoveMilitary(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'Nam' => 'required',
        ]);
        if ($validator->fails()) {
            // Bad Request
            return response()->json(['status' => "Failed", 'Err_Message' => 'Thiếu dữ liệu đầu vào']);
        }
       try {
        $result=$this->CreateReportMove($request);
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

    //thong ke tinh trang cap phat giay xac nhan nvqs
    private function CreateReportConfirm($request)
    {
        $student=Tb_sinhvien::join('Tb_yeucau', 'Tb_yeucau.MaSinhVien', '=', 'Tb_sinhvien.MaSinhVien')
        ->join('Tb_giay_xn_truong', 'Tb_giay_xn_truong.MaYeuCau', '=', 'Tb_yeucau.MaYeuCau')
        ->join('Tb_lop', 'Tb_lop.MaLop', '=', 'Tb_sinhvien.MaLop')
        ->filter($request);
        $Learning=$student->select(
            DB::raw("
            sum(case when month(NgayCap)=1 then 1 else 0  END) as '1',
            sum(case when month(NgayCap)=2 then 1 else 0  END) as '2',
            sum(case when month(NgayCap)=3 then 1 else 0  END) as '3',
            sum(case when month(NgayCap)=4 then 1 else 0  END) as '4',
            sum(case when month(NgayCap)=5 then 1 else 0  END) as '5',
            sum(case when month(NgayCap)=6 then 1 else 0  END) as '6',
            sum(case when month(NgayCap)=7 then 1 else 0  END) as '7',
            sum(case when month(NgayCap)=8 then 1 else 0  END) as '8',
            sum(case when month(NgayCap)=9 then 1 else 0  END) as '9',
            sum(case when month(NgayCap)=10 then 1 else 0  END) as '10',
            sum(case when month(NgayCap)=11 then 1 else 0  END) as '11',
            sum(case when month(NgayCap)=12 then 1 else 0  END) as '12',
            count(Tb_giay_xn_truong.MaGiayXN_Truong) as Tong
            ")
        )->where('Tb_yeucau.TrangThaiXuLy', 'like', "%Đã cấp%");
        
        if($request->Khoas){
            $Learning = $Learning->where('Tb_lop.Khoas', '=', $request->Khoas);
        }
        $Learning = $Learning->first()->toArray();
        
        $chart=[];
        if($Learning['Tong']!=0)
        {
            foreach ($Learning as $key => $value) {
                if($key!='Tong')
                {
                    $chart[]=[$value];
                }
            }
        }
        return [$Learning['Tong'], $chart];
    }

    public function ReportConfirmMilitary(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'Nam' => 'required',
        ]);
        if ($validator->fails()) {
            // Bad Request
            return response()->json(['status' => "Failed", 'Err_Message' => 'Thiếu dữ liệu đầu vào']);
        }
       try {
        $result=$this->CreateReportConfirm($request);
        return response()->json([
            'status'=>"Success",
            'data'=>[
                "Total_Learning"=>$result[0],
                "Chart"=>$result[1]
            ]
            ]);
       } catch (Exception $e) {
        return response()->json(['status' => "Failed", 'Err_Message' => $e->getMessage()]);
       }
    }

    //xuat bao cao thong ke tinh trang cap phat giay xac nhan nvqs theo thang nam khoas
    public function ExportFileConfirm(Request $request){
        $validator = Validator::make($request->all(), [
            'Nam' => 'required',
        ]);
        if ($validator->fails()) {
            // Bad Request
            return response()->json(['status' => "Failed", 'Err_Message' => 'Thiếu dữ liệu đầu vào']);
        }
        try {
        $date=Carbon::now();
        $result=$this->CreateReportConfirm($request);
        if($request->Thang){
            $templateProcessor = new TemplateProcessor('TemplateReport/ExportConfirmMiliMonth.docx');
        }
        else{
            $templateProcessor = new TemplateProcessor('TemplateReport/ExportConfirmMili.docx');
        }
        $Export=[
            'Ngay'=>$date->day,
            'Thang'=>$date->month,
            'Nam'=>$date->year,
            'NamTk'=>$request->Nam,
            'ThangTK'=>$request->Thang ? "Tháng ".$request->Thang : "",
            'NgayTK'=>$request->Ngay ? "Ngày ".$request->Ngay : "",
            'Khoas'=>$request->Khoas ? "\n- Khóa: ".$request->Khoas : "",
            "Total_Learning"=>$request->Thang ? $result[1][(int)$request->Thang - 1][0] : $result[0],
        ];

        if($result[1]){
            foreach ($result[1] as $key=> $value) {
                $Export['I'.($key+1)]=$value[0];
            }
        }else{
            $month = ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12'];
            foreach ($month as $m) {
                $Export['I'.($m)]=0;
            }
        }
        $templateProcessor->cloneBlock('block_name', 0, true, false, [$Export]);
        $filename = "ReportConfirm";
        $templateProcessor->saveAs($filename . '.docx');
        return response()->download($filename . '.docx')->deleteFileAfterSend(true);
        } catch (Exception $e) {
            return response()->json(['status' => "Failed", 'Err_Message' => $e->getMessage()]);
        }
    }

    //xuat bao cao thong ke tinh trang cap phat giay gioi thieu di chuyen nvqs theo thang nam khoas
    public function ExportFileMove(Request $request){
        $validator = Validator::make($request->all(), [
            'Nam' => 'required',
        ]);
        if ($validator->fails()) {
            // Bad Request
            return response()->json(['status' => "Failed", 'Err_Message' => 'Thiếu dữ liệu đầu vào']);
        }
        try {
        $date=Carbon::now();
        $result=$this->CreateReportMove($request);
        if($request->Thang){
            $templateProcessor = new TemplateProcessor('TemplateReport/ExportMoveMiliMonth.docx');
        }
        else{
            $templateProcessor = new TemplateProcessor('TemplateReport/ExportMoveMili.docx');
        }
        $Export=[
            'Ngay'=>$date->day,
            'Thang'=>$date->month,
            'Nam'=>$date->year,
            'NamTk'=>$request->Nam,
            'ThangTK'=>$request->Thang ? "Tháng ".$request->Thang : "",
            'NgayTK'=>$request->Ngay ? "Ngày ".$request->Ngay : "",
            'Khoas'=>$request->Khoas ? "\n- Khóa: ".$request->Khoas : "",
            "Total_Learning"=>$request->Thang ? $result[2][(int)$request->Thang - 1][0] : $result[0],
            "Total_Out"=>$request->Thang ? $result[2][(int)$request->Thang - 1][1] : $result[1],
        ];
        if($result[2]){
            foreach ($result[2] as $key=> $value) {
                $Export['I'.($key+1)]=$value[0];
                $Export['O'.($key+1)]=$value[1];
            }
        }else{
            $month = ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12'];
            foreach ($month as $m) {
                $Export['I'.($m)]=0;
                $Export['O'.($m)]=0;
            }
        }

        $templateProcessor->cloneBlock('block_name', 0, true, false, [$Export]);
        $filename = "ReportConfirm";
        $templateProcessor->saveAs($filename . '.docx');
        return response()->download($filename . '.docx')->deleteFileAfterSend(true);
        } catch (Exception $e) {
            return response()->json(['status' => "Failed", 'Err_Message' => $e->getMessage()]);
        }
    }
}
