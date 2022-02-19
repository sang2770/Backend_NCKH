<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTbTrangthaiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('Tb_trangthai', function (Blueprint $table) {
            $table->string('SoQuyetDinh', 20);
            $table->dateTime('NgayQuyetDinh');
            $table->string('MaSinhVien', 20);
            $table->primary('MaSinhVien');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('Tb_trangthai');
    }
}
