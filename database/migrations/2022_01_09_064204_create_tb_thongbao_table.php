<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTbThongbaoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_thongbao', function (Blueprint $table) {
            $table->Increments('MaThongBao');
            $table->string('NoiDung', 200);
            $table->dateTime('ThoiGianTB');
            $table->unsignedInteger('MaTKSV');
            $table->foreign('MaTKSV')->references('MaTKSV')->on('tb_tk_sinhvien');
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
        Schema::dropIfExists('tb_thongbao');
    }
}
