<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTbGiayCnDangkyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_giay_cn_dangky', function (Blueprint $table) {
            $table->increments('MaGiayDK');
            $table->string('SoDangKy', 20);
            $table->dateTime('NgayDangKy');
            $table->string('NoiDangKy', 150);
            $table->string('DiaChiThuongTru', 150);
            $table->dateTime('NgayNop');
            $table->string('MaSinhVien', 20);
            $table->foreign('MaSinhVien')->references('MaSinhVien')->on('tb_sinhvien');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tb_giay_cn_dangky');
    }
}
