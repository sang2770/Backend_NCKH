<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator as ValidationValidator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class UpdateUser extends FormRequest
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
            "TenLop" => "required"
        ];
    }
    public function messages()
    {
        return [
            'Email.email' => 'Email Không đúng định dạng',
            'SoDienThoai.digits' => 'Số điện thoại không đúng định dạng',
        ];
    }
    protected function failedValidation(ValidationValidator $validator)
    {

        $errors = (new ValidationException($validator))->errors();
        $err = [];
        foreach ($errors as $key => $value) {
            $err[] = $value[0];
        }
        throw new HttpResponseException(response()->json(
            [
                'status' => "Failed",
                'Err_Message' => "Dữ liệu đầu vào sai!",
                "info" => $err,
                'status_code' => 422,
            ],
            JsonResponse::HTTP_UNPROCESSABLE_ENTITY
        ));
    }
}
