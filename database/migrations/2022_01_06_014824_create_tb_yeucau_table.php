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
            $table->string('MaGiayXN_Truong', 20);
            $table->string('MaSinhVien', 20);
            $table->dateTime('NgayYeuCau');
            $table->dateTime('NgayXuLy');
            $table->foreign('MaGiayXN_Truong')->references('MaGiayXN_Truong')->on('tb_giay_xn_truong');
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
