<?php

namespace App\Http\Controllers;

use App\Models\TaiKhoanQuanLy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
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
        if (Auth::attempt($input)) {
            $token = $request->user()->createToken('authToken')->accessToken;
            $result = ['status' => 'Success', 'Token_access' => $token, 'Token_type' => "Bearer ", 'user' => Auth::user()];
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
    // Override
    public function TenDangNhap()
    {
        return 'TenDangNhap';
    }

    // Đăng ký
    // public function signup(Request $request)
    // {

    //     $user = new TaiKhoanQuanLy([
    //         'Id' => 2,
    //         'TenDangNhap' => $request->TenDangNhap,
    //         'MatKhau' => Hash::make($request->MatKhau),
    //     ]);

    //     $user->save();

    //     return response()->json([
    //         'status' => 'success',
    //     ]);
    // }
}
