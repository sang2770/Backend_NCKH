<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTbGiayDcTruongTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_giay_dc_truong', function (Blueprint $table) {
            $table->increments('MaGiayDC_Truong');
            $table->string('SoGioiThieuDC', 20);
            $table->dateTime('NgayCap');
            $table->dateTime('NgayHH');
            $table->string('NoiChuyenVe', 150);
            $table->string('NoiOHienTai', 150);
            $table->string('LyDo', 100);
            $table->unsignedInteger('MaGiayDK');
            $table->foreign('MaGiayDK')->references('MaGiayDK')->on('tb_giay_cn_dangky');
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
        Schema::dropIfExists('tb_giay_dc_truong');
    }
}
