<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTbTkSinhvienTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('Tb_tk_sinhvien', function (Blueprint $table) {
            $table->increments('MaTKSV');
            $table->string('MatKhau', 60);
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
        Schema::dropIfExists('Tb_tk_sinhvien');
    }
}
