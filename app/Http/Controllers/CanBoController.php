<?php

namespace App\Http\Controllers;

use App\Models\Tb_canbo;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
class CanBoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // loc theo tinh trang 
        $TrangThai=$request->query('TrangThai');
        $limit = $request->query('limit');
        $page = $request->query('page');
        try {
            $canbo=Tb_canbo::where('TrangThai', 'like', '%'.$TrangThai.'%')->paginate($limit, [
                'MaCanBo', 'HoVaTen', 'TrangThai', 'ChucVu', 'ThoiGianBatDau', 'ThoiGianKetThuc'
            ], 'page', $page)->toArray();;
            return response()->json(['status' => "Success", 'data' => $canbo['data'], 'pagination' => [
                "page" => $canbo['current_page'],
                "limit" => $limit,
                "TotalPage" => $canbo['last_page']
            ]]);
        } catch (Exception $e) {
            return response()->json(['status' => "Failed", 'Err_Message' => $e->getMessage()]);
            
        }
    }
    public function Show(){
        try {
            $canbo=Tb_canbo::select("*")
            ->where('TrangThai', 'Đang hoạt động')
            ->where('ChucVu', 'Phó chỉ huy trưởng')
            ->pluck("HoVaTen");
            return response()->json(['status' => "Success", 'data' => $canbo]);
        } catch (Exception $e) {
            return response()->json(['status' => "Failed", 'Err_Message' => $e->getMessage()]);
        }
    }

    public function ShowCaptain(){
        try {
            $canbo=Tb_canbo::select("*")
            ->where('TrangThai', 'Đang hoạt động')
            ->where('ChucVu', 'Chỉ huy trưởng')
            ->pluck("HoVaTen");
            return response()->json(['status' => "Success", 'data' => $canbo]);
        } catch (Exception $e) {
            return response()->json(['status' => "Failed", 'Err_Message' => $e->getMessage()]);
        }
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator=Validator::make($request->all(), [
            'HoVaTen'=>"required",
            "ChucVu"=>"required",
            "ThoiGianBatDau"=>"required",
            "ThoiGianKetThuc"=>"required"
        ]);
        if ($validator->fails()) {
            // Bad Request
            return response()->json(['status' => "Failed", 'Err_Message' => 'Dữ liệu đầu vào không chính xác']);
        }
        try {
            $user=$validator->validated();
            $user['TrangThai']="Đang hoạt động";
            // $user['HoVaTen']=Str::lower($user['HoVaTen']);
            // $HoTen= $user['HoVaTen'];
            // $HoTen=explode(" ", $HoTen);
            // $convert="";
            // foreach($HoTen as $item)
            // {
            //     $convert=$convert.' '.ucfirst($item);
            // }
            // $user['HoVaTen']=$convert;

            Tb_canbo::create($user);
            return response()->json(['status' => "Success", 'data'=> $validator->validated()]);
        } catch (Exception $e) {
            return response()->json(['status' => "Failed", 'Err_Message' => 'Có lỗi vui lòng thử lại! Hoặc liên hệ bộ phận kĩ thuậtg']);
        }
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $validator=Validator::make($request->all(),[
            'MaCanBo'=>"required",
            'HoVaTen'=>"required",
            "ChucVu"=>"required",
            "ThoiGianBatDau"=>"required",
            "ThoiGianKetThuc"=>"required"
        ]);
        if ($validator->fails()) {
            // Bad Request
            return response()->json(['status' => "Failed", 'Err_Message' => 'Dữ liệu đầu vào không chính xác']);
        }
        $canbo=Tb_canbo::find($request->MaCanBo);
        if($canbo)
        {
            $input=$request->input();
            unset($input["_method"]);
            $canbo->update($input);
            return response()->json(["status"=>"Success", "data"=>$input]);

        }else{
            return response()->json(['status' => "Failed", 'Err_Message' => "NotFound"]);

        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $canbo=Tb_canbo::find($id);
        if($canbo)
        {

            $canbo->update(['TrangThai' => 'Nghỉ hưu']);
            return response()->json(["status"=>"Success"]);
        }
        else{
            return response()->json(['status' => "Failed", 'Err_Message' => "NotFound"]);

        }
    }
}
