<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTbCanbo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_canbo', function (Blueprint $table) {
            $table->increments('MaCanBo');
            $table->string('HoVaTen', 150);
            $table->string('TrangThai', 150);
            $table->string('ChucVu', 150);
            $table->date('ThoiGianKetThuc')->nullable();
            $table->date('ThoiGianBatDau');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tb_canbo');
    }
}
