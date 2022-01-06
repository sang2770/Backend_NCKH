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
        Schema::create('tb_khoa', function (Blueprint $table) {
            $table->string('MaKhoa', 20);
            $table->string('TenKhoa', 50);
            $table->string('DiaChi', 100);
            $table->string('SoDienThoai', 10);
            $table->primary('Makhoa');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tb_khoa');
    }
}
