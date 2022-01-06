<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTbCmtndTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_cmtnd', function (Blueprint $table) {
            $table->string('SoCMTND', 20);
            $table->string('NoiCap_CMTND', 150);
            $table->string('NgayCap_CMTND', 20);
            $table->string('MaSinhVien', 20);
            $table->primary('SoCMTND');
            $table->foreign('MaSinhVien')->references('MaSinhVien')->on('tb_sinhvien');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tb_cmtnd');
    }
}
