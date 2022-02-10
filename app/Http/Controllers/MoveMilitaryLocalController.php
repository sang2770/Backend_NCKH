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
            $MagiayDK = Tb_giay_cn_dangky::select('MaGiayDK')->where('Tb_giay_cn_dangky.MaSinhVien', '=', $Input['MaSinhVien'])->get();
            $LyDo = "Trúng tuyển đại học, cao đẳng";
            return [
                'SoGioiThieu'          => $Input['SoGioiThieu'],
                'NgayCap'              => $Input['NgayCap'],
                'NgayHH'               => $Input['NgayHH'],
                'NoiOHienTai'          => $Input['NoiOHienTai'],
                'NoiChuyenDen'         => $Input['NoiChuyenDen'],
                'LyDo'                 => $LyDo,
                'BanChiHuy'            => $Input['BanChiHuy'],
                'MaGiayDK'             => $MagiayDK[0]['MaGiayDK'],
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
            return response()->json(['status' => "Failed", 'Err_Message' => $e->getMessage()]);
        }
    }

    //update
    public function edit($id){
        $edit = Tb_giay_dc_diaphuong::where('MaGiayDC_DP', $id)->first();
        return $edit;
    }

    public function Update(UpdateMoveLocalRequest $request, $id){
        $request->validated();
        if(Tb_giay_dc_diaphuong::where('MaGiayDC_DP', $id)->exists()){
            $task = $this->edit($id);
            $input = $request->only('SoGioiThieu', 'NgayCap', 'NgayHH', 'NoiOHienTai', 'NoiChuyenDen', 'BanChiHuy');
            $task->fill($input)->save();
            return response()->json(['status' => "Success updated"]);
        }
        else{
            return response()->json(['status' => "Not Found!"]);
        }
    }
    //show lần cấp của từng sinh viên
    public function show(Request $request, $id){
        $limit = $request->query('limit');
        $page = $request->query('page');
        $info = Tb_sinhvien::join('Tb_yeucau', 'Tb_yeucau.MaSinhVien', '=', 'Tb_sinhvien.MaSinhVien')
                            ->join('Tb_giay_xn_truong','Tb_giay_xn_truong.MaYeuCau', '=', 'Tb_yeucau.MaYeuCau')
                            ->select('Tb_giay_xn_truong.NgayCap')
                            ->where('Tb_sinhvien.MaSinhVien', '=', $id);

        $info = $info->paginate($perPage = $limit, $columns = ['*'], $pageName = 'page', $page)->toArray();
        return response()->json(['status' => "Success", 'data' => $info["data"], 'pagination' => [
            "page" => $info['current_page'],
            "first_page_url"    => $info['first_page_url'],
            "next_page_url"     => $info['next_page_url'],
            "TotalPage"         => $info['last_page']
        ]]);
    }
}
