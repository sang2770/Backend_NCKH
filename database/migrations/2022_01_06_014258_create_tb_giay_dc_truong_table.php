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
            $table->string('MaGiayDC_Truong', 20);
            $table->dateTime('NgayCap');
            $table->string('LyDo', 100);
            $table->dateTime('NgayHH');
            $table->string('NoiChuyenVe', 150);
            $table->string('NoiOHienTai', 150);
            $table->primary('MaGiayDC_Truong');
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
