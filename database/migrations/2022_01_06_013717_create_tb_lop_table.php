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
            $table->string('MaLop', 20);
            $table->string('TenLop', 50);
            $table->string('Khoa', 20);
            $table->string('MaKhoa', 20);
            $table->primary('Malop');
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
