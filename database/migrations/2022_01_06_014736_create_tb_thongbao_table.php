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
            $table->string('MaThongBao', 20);
            $table->string('NoiDung', 100);
            $table->dateTime('ThoiGianTB');
            $table->string('MaTKSV', 20);
            $table->primary('MaThongBao');
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
