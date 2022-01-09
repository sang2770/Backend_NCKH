<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTbLopTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_lop', function (Blueprint $table) {
            $table->increments('MaLop');
            $table->string('TenLop', 60);
            $table->string('Khoas', 20);
            $table->unsignedInteger('MaKhoa');
            $table->foreign('MaKhoa')->references('MaKhoa')->on('tb_khoa');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tb_lop');
    }
}
