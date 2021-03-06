<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTbKhoaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('Tb_khoa', function (Blueprint $table) {
            $table->increments('MaKhoa');
            $table->string('TenKhoa', 100);
            $table->string('DiaChi', 100)->nullable();
            $table->string('SoDienThoai', 11)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        
        Schema::dropIfExists('Tb_khoa');
    }
}
