<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTbThongbaosvTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('Tb_thongbaosv', function (Blueprint $table) {
            $table->dateTime('ThoiGianTB');
            $table->unsignedInteger('MaTKSV');
            $table->unsignedInteger('MaThongBaoChinh');
            $table->foreign('MaTKSV')->references('MaTKSV')->on('Tb_tk_sinhvien');
            $table->foreign('MaThongBaoChinh')->references('MaThongBaoChinh')->on('Tb_thongbaochinh');
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
        Schema::dropIfExists('Tb_thongbaosv');
    }
}
