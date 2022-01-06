<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTbTkQuanlyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_tk_quanly', function (Blueprint $table) {
            $table->string('MaTK', 20);
            $table->string('TenDangNhap', 60);
            $table->string('MatKhau', 60);
            $table->primary('MaTK');
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
        Schema::dropIfExists('tb_tk_quanly');
    }
}
