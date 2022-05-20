<?php

namespace App\Http\Controllers;

use App\Http\Requests\MoveMilitaryLocalRequest;
use App\Http\Requests\UpdateMoveLocalRequest;
use App\Imports\MoveMilitaryLocalImport;
use App\Models\Tb_giay_cn_dangky;
use App\Models\Tb_giay_dc_diaphuong;
use App\Models\Tb_sinhvien;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Validator;

class MoveMilitaryLocalController extends Controller
{
    //import file
    public function StoreFile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required',
        ]);
        if ($validator->fails()) {
            // Bad Request
            return response()->json($validator->getMessageBag(), 400);
        }
        $import = new MoveMilitaryLocalImport();
        $import->import($request->file);
        if (!$import->failures()->isNotEmpty() && count($import->Err) == 0) {
            return response()->json(['status' => "Success"]);
        } else {
            $errors = [];
            $errorsMaDK = [];
            foreach ($import->failures() as $value) {
                $errors[] = ['row' => $value->row(), 'err' => $value->errors()];
            }
            foreach ($import->Err as $value) {
                $errorsMaDK[] = ['row' => $value['row'], 'err' => $value['err']];
            }
            return response()->json(['status' => "Failed", 'Err_Message' => 'Dữ liệu đầu vào sai!', 'err' => $errors, 'errMaDK' => $errorsMaDK]);
        }
    }

    public function create($Input){
        try {
            $LyDo = "Trúng tuyển đại học, cao đẳng";

            $date = date_create($Input['NgayCap']);
            $NgayCap = date_format($date, 'Y-m-d H:i:s');

            return [
                'SoGioiThieu'          => $Input['SoGioiThieu'],
                'NgayCap'              => $NgayCap,
                'NoiOHienTai'          => $Input['NoiOHienTai'],
                'NoiChuyenDen'         => $Input['NoiChuyenDen'],
                'LyDo'                 => $LyDo,
                'BanChiHuy'            => $Input['BanChiHuy'],
                'MaSinhVien'           => $Input['MaSinhVien'],
            ];
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    //thêm từng giấy dc dia phuong
    public function Store(MoveMilitaryLocalRequest $request){
        $validated = $request->validated();
        try {
            $validated = $this->create($request->all());
            Tb_giay_dc_diaphuong::insert($validated);
            return response()->json(['status' => "Success", 'data' => ["ThongTin" => $validated]]);
        } catch (Exception $e) {
            return response()->json(['status' => "Failed", 'Err_Message' => $request->MaSinhVien]);
        }
    }


    //update
    public function createRegister($Input){
        try {
            $date = date_create($Input['NgayDangKy']);
            $NgayDK = date_format($date, 'Y-m-d H:i:s');

            $date2 = date_create($Input['NgayNop']);
            $NgayNop = date_format($date2, 'Y-m-d H:i:s');
            
            return [
                'SoDangKy'          => $Input['SoDangKy'],
                'NgayDangKy'        => $NgayDK,
                'NoiDangKy'         => $Input['NoiDangKy'],
                'DiaChiThuongTru'   => $Input['DiaChiThuongTru'],
                'NgayNop'           => $NgayNop,
            ];
        } catch (\Throwable $th) {
            throw $th;
        }
    }
    public function updateMove($Input){
        try {
            $LyDo = "Trúng tuyển đại học, cao đẳng";

            $date = date_create($Input['NgayCap']);
            $NgayCap = date_format($date, 'Y-m-d H:i:s');

            return [
                'SoGioiThieu'          => $Input['SoGioiThieu'],
                'NgayCap'              => $NgayCap,
                'NoiOHienTai'          => $Input['NoiOHienTai'],
                'NoiChuyenDen'         => $Input['NoiChuyenDen'],
                'LyDo'                 => $LyDo,
                'BanChiHuy'            => $Input['BanChiHuy'],
            ];
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function edit($id)
    {
        $edit = Tb_giay_cn_dangky::where('MaSinhVien', $id)->first();
        return $edit;
    }

    public function editMove($id)
    {
        $edit = Tb_giay_dc_diaphuong::where('tb_giay_dc_diaphuong.MaSinhVien', $id)->first();
        return $edit;
    }

    public function Update(UpdateMoveLocalRequest $request, $id)
    {
        $request->validated();
        if (Tb_giay_cn_dangky::where('MaSinhVien', $id)->exists() && 
            Tb_giay_dc_diaphuong::where('tb_giay_dc_diaphuong.MaSinhVien', $id)->exists()) {
            $task = $this->edit($id);
            $input = $this->createRegister($request->all());
            $task->fill($input)->save();

            $taskMove = $this->editMove($id);
            $inputMove = $this->updateMove($request->all());
            $taskMove->fill($inputMove)->save();

            return response()->json(['status' => "Success updated"]);
        } else {
            return response()->json(['status' => "Not Found!"]);
        }

        // if(){
        //     $task = $this->editMove($id);
        //     $input = $request->only('SoGioiThieu', 'NgayCap', 'NgayHH', 'NoiOHienTai', 'NoiChuyenDen', 'BanChiHuy');
        //     $task->fill($input)->save();
        //     return response()->json(['status' => "Success updated"]);
        // }
        // else{
        //     return response()->json(['status' => "Not Found!"]);
        // }
    }
    //show lần cấp của từng sinh viên
    public function show(Request $request, $id){
        $limit = $request->query('limit');
        $page = $request->query('page');
        $info = Tb_sinhvien::join('tb_yeucau', 'tb_yeucau.MaSinhVien', '=', 'tb_sinhvien.MaSinhVien')
                            ->join('tb_giay_xn_truong','tb_giay_xn_truong.MaYeuCau', '=', 'tb_yeucau.MaYeuCau')
                            ->select('tb_giay_xn_truong.NgayCap')
                            ->where('tb_sinhvien.MaSinhVien', '=', $id);

        $info = $info->paginate($perPage = $limit, $columns = ['*'], $pageName = 'page', $page)->toArray();
        return response()->json(['status' => "Success", 'data' => $info["data"], 'pagination' => [
            "page" => $info['current_page'],
            "first_page_url"    => $info['first_page_url'],
            "next_page_url"     => $info['next_page_url'],
            "TotalPage"         => $info['last_page']
        ]]);
    }
}
