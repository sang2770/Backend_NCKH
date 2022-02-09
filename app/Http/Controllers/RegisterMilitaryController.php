<?php

namespace App\Http\Controllers;
use App\Models\Tb_giay_cn_dangky;
use App\Imports\RegisterMilitaryImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Exception;
use App\Http\Requests\RegisterMilitaryRequest;
use App\Http\Requests\UpdateRegister;
use App\Models\Tb_sinhvien;
use Illuminate\Support\Facades\Validator;

class RegisterMilitaryController extends Controller
{
    // Import file
    public function StoreFile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required',
        ]);
        if ($validator->fails()) {
            // Bad Request
            return response()->json($validator->getMessageBag(), 400);
        }
        $import = new RegisterMilitaryImport();
        $import->import($request->file);
        if (!$import->failures()->isNotEmpty() && count($import->Err) == 0) {
            return response()->json(['status' => "Success"]);
        } else {
            $errors = [];
            $errorsMSV = [];
            foreach ($import->failures() as $value) {
                $errors[] = ['row' => $value->row(), 'err' => $value->errors()];
            }
            foreach ($import->Err as $value) {
                $errorsMSV[] = ['row' => $value['row'], 'err' => $value['err']];
            }
            return response()->json(['status' => "Failed", 'Err_Message' => 'Dữ liệu đầu vào sai!', 'err' => $errors, 'errMSV' => $errorsMSV]);
        }
    }

    //thêm từng giấy cn dky
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

    //update
    public function edit($id){
        $edit = Tb_giay_cn_dangky::findOrFail($id);
        return $edit;
    }

    public function Update(RegisterMilitaryRequest $request, $id){
        if(Tb_giay_cn_dangky::where('MaSinhVien', $id)->exists()){
            $task = $this->edit($id);
            $input = $request->only('SoDangKy', 'NgayDangKy', 'NoiDangKy', 'DiaChiThuongTru', 'NgayNop');
            $task->fill($input)->save();
            return response()->json(['status' => "Success updated"]);
        }
        else{
            return response()->json(['status' => "Not Found!"]);
        }
    }
    /// loc ra thong tin sinh vien kem thong tin giay chung nhan dky
    public function FilterRegister(Request $request){
        $limit = $request->query('limit');
        $page = $request->query('page');
        $info = Tb_sinhvien::join('Tb_lop', 'Tb_lop.MaLop', '=', 'Tb_sinhvien.MaLop')
                            ->join('Tb_khoa','Tb_khoa.MaKhoa', '=', 'Tb_lop.MaKhoa')
                            ->join('Tb_giay_cn_dangky', 'Tb_giay_cn_dangky.MaSinhVien', '=' , 'Tb_sinhvien.MaSinhVien')
                            ->select('Tb_sinhvien.HoTen', 'Tb_sinhvien.MaSinhVien', 'Tb_sinhvien.NgaySinh', 'Tb_lop.TenLop', 
                            'Tb_khoa.TenKhoa', 'Tb_lop.Khoas', 'Tb_giay_cn_dangky.SoDangKy', 'Tb_giay_cn_dangky.NoiDangKy', 'Tb_giay_cn_dangky.DiaChiThuongTru', 'Tb_giay_cn_dangky.NgayDangKy', 'Tb_giay_cn_dangky.NgayNop');

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
        $info = $info->paginate($perPage = $limit, $columns = ['*'], $pageName = 'page', $page)->toArray();
        return response()->json(['status' => "Success", 'data' => $info["data"], 'pagination' => [
            "page" => $info['current_page'],
            "first_page_url"    => $info['first_page_url'],
            "next_page_url"     => $info['next_page_url'],
            "TotalPage"         => $info['last_page']
        ]]);
    }

    /// loc ra thong tin sinh vien kem thong tin giay xac nhan tu truong
    public function FilterConfirm(Request $request){
        $limit = $request->query('limit');
        $page = $request->query('page');
        $info = Tb_sinhvien::join('Tb_lop', 'Tb_lop.MaLop', '=', 'Tb_sinhvien.MaLop')
                            ->join('Tb_khoa','Tb_khoa.MaKhoa', '=', 'Tb_lop.MaKhoa')
                            ->join('Tb_yeucau', 'Tb_yeucau.MaSinhVien', '=' , 'Tb_sinhvien.MaSinhVien')
                            ->select('Tb_sinhvien.HoTen', 'Tb_sinhvien.MaSinhVien', 'Tb_sinhvien.NgaySinh', 'Tb_lop.TenLop', 'Tb_khoa.TenKhoa', 'Tb_lop.Khoas', 'Tb_yeucau.TrangThaiXuLy');

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
        if($request->TrangThaiXuLy){
            $info = $info->where('Tb_yeucau.TrangThaiXuLy', '=', $request->TrangThaiXuLy);
        }
        
        $info = $info->where(function ($query) {
            $query->where('Tb_yeucau.TrangThaiXuLy', '=', 'Đã xử lý')
                ->orWhere('Tb_yeucau.TrangThaiXuLy', '=', 'Đã cấp');
        });

        $info = $info->paginate($perPage = $limit, $columns = ['*'], $pageName = 'page', $page)->toArray();
        return response()->json(['status' => "Success", 'data' => $info["data"], 'pagination' => [
            "page" => $info['current_page'],
            "first_page_url"    => $info['first_page_url'],
            "next_page_url"     => $info['next_page_url'],
            "TotalPage"         => $info['last_page']
        ]]);
    }

     /// loc ra thong tin sinh vien kem thong tin giay di chuyen nvqs tu truong
     public function FilterMove(Request $request){
        $limit = $request->query('limit');
        $page = $request->query('page');
        $info = Tb_sinhvien::join('Tb_lop', 'Tb_lop.MaLop', '=', 'Tb_sinhvien.MaLop')
                            ->join('Tb_khoa','Tb_khoa.MaKhoa', '=', 'Tb_lop.MaKhoa')
                            ->join('Tb_trangthai', 'Tb_trangthai.MaSinhVien', '=' , 'Tb_sinhvien.MaSinhVien')
                            ->select('Tb_sinhvien.HoTen', 'Tb_sinhvien.MaSinhVien', 'Tb_sinhvien.NgaySinh', 'Tb_lop.TenLop', 'Tb_khoa.TenKhoa', 'Tb_lop.Khoas', 'Tb_sinhvien.TinhTrangSinhVien', 'Tb_trangthai.SoQuyetDinh', 'Tb_trangthai.NgayQuyetDinh');

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
        if($request->TinhTrangSinhVien){
            $info = $info->where('Tb_sinhvien.TinhTrangSinhVien', '=', $request->TinhTrangSinhVien);
        }

        $info = $info->where(function ($query) {
            $query->where('Tb_sinhvien.TinhTrangSinhVien', '=', 'Đã tốt nghiệp')
                ->orWhere('Tb_sinhvien.TinhTrangSinhVien', '=', 'Đã thôi học');
        });

        $info = $info->paginate($perPage = $limit, $columns = ['*'], $pageName = 'page', $page)->toArray();
        return response()->json(['status' => "Success", 'data' => $info["data"], 'pagination' => [
            "page" => $info['current_page'],
            "first_page_url"    => $info['first_page_url'],
            "next_page_url"     => $info['next_page_url'],
            "TotalPage"         => $info['last_page']
        ]]);
    }
     /// loc ra thong tin sinh vien kem thong tin giay di chuyen nvqs tu địa phương
     public function FilterMoveLocal(Request $request){
        $limit = $request->query('limit');
        $page = $request->query('page');
        $info = Tb_sinhvien::join('Tb_lop', 'Tb_lop.MaLop', '=', 'Tb_sinhvien.MaLop')
                            ->join('Tb_khoa','Tb_khoa.MaKhoa', '=', 'Tb_lop.MaKhoa')
                            ->join('Tb_giay_cn_dangky', 'Tb_giay_cn_dangky.MaSinhVien', '=' , 'Tb_sinhvien.MaSinhVien')
                            ->join('Tb_giay_dc_diaphuong', 'Tb_giay_cn_dangky.MaGiayDK', '=' , 'Tb_giay_dc_diaphuong.MaGiayDK')
                            ->select('Tb_sinhvien.HoTen', 'Tb_sinhvien.MaSinhVien', 'Tb_sinhvien.NgaySinh', 'Tb_lop.TenLop', 
                            'Tb_khoa.TenKhoa', 'Tb_lop.Khoas', 'Tb_giay_dc_diaphuong.SoGioiThieu', 'Tb_giay_dc_diaphuong.BanChiHuy',
                            'Tb_giay_dc_diaphuong.NgayCap', 'Tb_giay_dc_diaphuong.NgayHH', 'Tb_giay_dc_diaphuong.NoiOHienTai', 'Tb_giay_dc_diaphuong.NoiChuyenDen');

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

        $info = $info->paginate($perPage = $limit, $columns = ['*'], $pageName = 'page', $page)->toArray();
        return response()->json(['status' => "Success", 'data' => $info["data"], 'pagination' => [
            "page" => $info['current_page'],
            "first_page_url"    => $info['first_page_url'],
            "next_page_url"     => $info['next_page_url'],
            "TotalPage"         => $info['last_page']
        ]]);
    }
}
