<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator as ValidationValidator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;


class AddStudentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            "MaSinhVien" => "required|unique:Tb_sinhvien",
            'HoTen' => "required",
            "NgaySinh" => "required",
            "NoiSinh" => "required",
            "Email" => "required|email",
            "GioiTinh" => "required",
            "TonGiao" => "required",
            "QuocTich" => "required",
            "DanToc" => "required",
            "SDT" => "required|digits:10",
            "DiaChiBaoTin" => "required",
            "HeDaoTao" => "required",
            "TinhTrangSinhVien" => "required",
            "NoiSinh" => "required",
            "HoKhauTinh" => "required",
            "HoKhauHuyen" => "required",
            "HoKhauXaPhuong" => "required",
            "TenLop" => "required",
            "SoCMTND" => "required",
            "NgayCapCMTND" => "required",
            "NoiCapCMTND" => "required",
        ];
    }
    public function messages()
    {
        return [
            'Email.email' => 'Invalid email format',
            'SoDienThoai.digits' => 'Invalid phone number format',
        ];
    }
    protected function failedValidation(ValidationValidator $validator)
    {

        $errors = (new ValidationException($validator))->errors();
        throw new HttpResponseException(response()->json(
            [
                'status' => "Failed",
                'Err_Message' => $errors,
                'status_code' => 422,
            ],
            JsonResponse::HTTP_UNPROCESSABLE_ENTITY
        ));
    }
}
