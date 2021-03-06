<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTbGiayDcDiaphuongTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('Tb_giay_dc_diaphuong', function (Blueprint $table) {
            $table->increments('MaGiayDC_DP');
            $table->string('SoGioiThieu', 20)->nullable();;
            $table->date('NgayCap');
            $table->string('NoiOHienTai', 150);
            $table->string('NoiChuyenDen', 150);
            $table->string('LyDo', 100);
            $table->string('BanChiHuy', 200);
            $table->string('MaSinhVien', 20);
            $table->foreign('MaSinhVien')->references('MaSinhVien')->on('Tb_sinhvien');
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
        Schema::dropIfExists('Tb_giay_dc_diaphuong');
    }
}
