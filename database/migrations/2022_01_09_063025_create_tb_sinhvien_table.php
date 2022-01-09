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
            $table->string('HoTen', 100);
            $table->dateTime('NgaySinh');
            $table->string('NoiSinh', 100);
            $table->string('GioiTinh', 20);
            $table->string('DanToc', 20);
            $table->string('TonGiao', 20);
            $table->string('QuocTich', 20);
            $table->string('DiaChiBaoTin', 100);
            $table->string('SDT', 11);
            $table->string('Email', 50);
            $table->string('HoKhauTinh', 50);
            $table->string('HoKhauHuyen', 50);
            $table->string('HoKhauXaPhuong', 50);
            $table->string('TinhTrangSinhVien', 50);
            $table->string('HeDaoTao', 50);
            $table->unsignedInteger('MaLop');
            $table->primary('MaSinhVien');
            $table->foreign('MaLop')->references('MaLop')->on('tb_lop');
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
