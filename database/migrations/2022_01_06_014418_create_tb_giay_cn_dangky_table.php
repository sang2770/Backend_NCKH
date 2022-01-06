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
            $table->string('SoDangKy', 20);
            $table->dateTime('NgayDangKy');
            $table->string('NoiDangKy', 150);
            $table->string('DiaChiThuongTru', 150);
            $table->dateTime('NgayNop');
            $table->string('MaGiayDC_Truong', 20);
            $table->primary('SoDangKy');
            $table->foreign('MaGiayDC_Truong')->references('MaGiayDC_Truong')->on('tb_giay_dc_truong');
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
