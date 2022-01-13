<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTbThongbaochinhTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_thongbaochinh', function (Blueprint $table) {
            $table->Increments('MaThongBaoChinh');
            $table->string('TieuDeTB', 200);
            $table->string('NoiDungTB', 200);
            $table->dateTime('ThoiGianTao');
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
        Schema::dropIfExists('tb_thongbaochinh');
    }
}
