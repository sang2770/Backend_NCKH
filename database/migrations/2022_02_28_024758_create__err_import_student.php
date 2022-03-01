<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateErrImportStudent extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_ErrImportStudent', function (Blueprint $table) {
            $table->id();
            $table->string("NoiDung", 1000);
            $table->dateTime("ThoiGian");
            $table->string("TrangThai", 50);
            $table->unsignedInteger("MaTK");
            $table->foreign("MaTK")->references("MaTK")->on("Tb_tk_quanly");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tb_ErrImportStudent');
    }
}
