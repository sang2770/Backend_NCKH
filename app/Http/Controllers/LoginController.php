<?php

namespace App\Http\Controllers;

use App\Models\Tb_tk_quanly;
use Illuminate\Http\Request;
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
        if (auth()->guard('admin')->attempt($input)) {
            config(['auth.guards.api.provider' => 'admin']);
            $user = Tb_tk_quanly::select('*')->find(auth()->guard('admin')->user()->MaTK);

            $token = $user->createToken('authToken', ['admin'])->accessToken;
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
        // var_dump("Me");
        $user = $request->user();
        $result = ['status' => 'Success',  'user' => $user];
        return response()->json($result);
    }
    // Override
    public function TenDangNhap()
    {
        return 'TenDangNhap';
    }
}
