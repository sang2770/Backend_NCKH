<?php

namespace App\Http\Controllers;

use App\Models\Tb_tk_sinhvien;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class LoginClientController extends Controller
{
    public function Login(Request $request)
    {
        $validator = Validator::make($request->input(), [
            'TenDangNhap' => 'required',
            'password' => 'required'
        ]);
        if ($validator->fails()) {
            // Bad Request
            return response()->json($validator->getMessageBag(), 400);
        }
        $input = $request->only('TenDangNhap', 'password');
        if (auth()->guard('user')->attempt($input)) {
            config(['auth.guards.api.provider' => 'user']);
            $user = Tb_tk_sinhvien::find(auth()->guard('user')->user()->MaTKSV);
            $token = $user->createToken('authToken', ['user'])->accessToken;
            $result = ['status' => 'Success', 'Token_access' => $token, 'Token_type' => "Bearer ", 'user' => $user];
            return response()->json($result);
        } else {
            return response()->json(['status' => 'Failed', 'Err_Message' => "Tài khoản hoặc mật khẩu không đúng"]);
        }
    }
    // Đăng xuất
    public function Logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json(['status' => 'Success']);
    }
    // Get User
    public function user(Request $request)
    {
        $result = ['status' => 'Success',  'user' => $request->user()];
        return response()->json($result);
    }
    // Get User
    public function change(Request $request)
    {
        var_dump($request->user());
        $validator = Validator::make($request->input(), [
            'MaSinhVien' => 'required',
            'TenDangNhap' => 'required',
            'Old' => 'required',
            'New' => 'required',
            'New_Repeat' => "required"
        ]);
        if ($validator->fails()) {
            // Bad Request
            return response()->json($validator->getMessageBag(), 400);
        }
        try {
            $Id = $request->MaSinhVien;
            $Name = $request->TenDangNhap;
            $New = Hash::make($request->New);
            $user = Tb_tk_sinhvien::where('MaSinhVien', $Id)->where("TenDangNhap", $Name)->first();;
            if (!$user) {
                return response()->json(['status' => 'Failed', 'Err_Message' => "Not Found"]);
            } elseif (!Hash::check($request->Old, $user->MatKhau)) {
                return response()->json(['status' => 'Failed', 'Err_Message' => "Mật khẩu không chính xác!"]);
            } elseif ($request->New != $request->New_Repeat) {
                return response()->json(['status' => 'Failed', 'Err_Message' => "Mật khẩu không trùng khớp"]);
            } else {
                Tb_tk_sinhvien::where("MaSinhVien", $Id)->update(['MatKhau' => $New]);
                $request->user()->token()->revoke();
                return response()->json(['status' => 'Success']);
            }
        } catch (Exception $e) {
            return response()->json(['status' => 'Failed', 'Err_Message' => $e->getMessage()]);
        }
    }
    // Override
    public function TenDangNhap()
    {
        return 'TenDangNhap';
    }
}
