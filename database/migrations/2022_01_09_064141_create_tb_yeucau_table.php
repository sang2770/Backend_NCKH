<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTbYeucauTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_yeucau', function (Blueprint $table) {
            $table->Increments('MaYeuCau');
            $table->string('MaSinhVien', 20);
            $table->dateTime('NgayYeuCau');
            $table->dateTime('NgayXuLy');
            $table->string('TrangThaiXuLy', 100);
            $table->foreign('MaSinhVien')->references('MaSinhVien')->on('tb_sinhvien');
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
        Schema::dropIfExists('tb_yeucau');
    }
}
