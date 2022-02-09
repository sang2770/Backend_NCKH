<?php

namespace App\Http\Controllers;

use App\Models\PasswordReset;
use App\Models\Tb_tk_quanly;
use App\Notifications\ResetPasswordRequest;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ForgotPassWord extends Controller
{
    public function Forgot(Request $request)
    {
        try {
            $request->validate(['email' => "required|email"]);
            $user = Tb_tk_quanly::where('TenDangNhap', $request->email)->firstOrFail();
            $passwordReset = PasswordReset::updateOrCreate([
                'email' => $user->TenDangNhap,
            ], [
                'token' => Str::random(60),
            ]);
            if ($passwordReset) {
                $user->notify(new ResetPasswordRequest($passwordReset->token));
            }
            return response()->json([
                'status' => "Success",
                'Message' => 'We have e-mailed your password reset link!'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => "Failed",
                "Err_Message" => $e->getMessage()
            ]);
        }
    }
    public function Reset(Request $request)
    {
        try {
            $passwordReset = PasswordReset::where('token', $request->token)->firstOrFail();
            if (Carbon::parse($passwordReset->updated_at)->addMinutes(720)->isPast()) {
                $passwordReset->delete();
                return response()->json([
                    'status' => "Failed",
                    'Err_Message' => 'This password reset token is invalid.',
                ], 422);
            }
            $user = Tb_tk_quanly::where('TenDangNhap', $passwordReset->email)->firstOrFail();
            $updatePasswordUser = $user->update(['MatKhau' => Hash::make($request->MatKhau)]);
            $passwordReset->delete();

            return response()->json([
                'status' => "Success",
                'Message' => "Bạn đã thay đổi thành công vui lòng login lại.",
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => "Failed",
                "Err_Message" => $e->getMessage()
            ]);
        }
    }
}
