<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBangLichSu extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('Tb_LichSu', function (Blueprint $table) {
            $table->increments("ID");
            $table->string("NoiDung", 500);
            $table->string("MaSinhVien");
            $table->dateTime("ThoiGian");
            $table->unsignedInteger("MaTK");
            $table->foreign("MaSinhVien")->references("MaSinhVien")->on("tb_sinhvien");
            $table->foreign("MaTK")->references("MaTK")->on("tb_tk_quanly");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('Tb_LichSu');
    }
}
