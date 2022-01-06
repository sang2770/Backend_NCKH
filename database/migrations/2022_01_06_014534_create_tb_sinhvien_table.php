<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTbSinhvienTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_sinhvien', function (Blueprint $table) {
            $table->string('MaSinhVien', 20);
            $table->string('HoTen', 50);
            $table->dateTime('NgaySinh');
            $table->string('NoiSinh', 50);
            $table->string('GioiTinh', 20);
            $table->string('DanToc', 20);
            $table->string('TonGiao', 20);
            $table->string('QuocTich', 20);
            $table->string('DiaChiBaoTin', 100);
            $table->string('SDT', 10);
            $table->string('Email', 20);
            $table->string('HoKhauTinh', 20);
            $table->string('HoKhauHuyen', 20);
            $table->string('HoKhauXaPhuong', 20);
            $table->string('TinhTrangSinhVien', 20);
            $table->string('HeDaoTao', 20);
            $table->string('MaLop', 20);
            $table->string('SoDangKy', 20);
            $table->primary('MaSinhVien');
            $table->foreign('MaLop')->references('MaLop')->on('tb_lop');
            $table->foreign('SoDangKy')->references('SoDangKy')->on('tb_giay_cn_dangky');
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
        Schema::dropIfExists('tb_sinhvien');
    }
}
