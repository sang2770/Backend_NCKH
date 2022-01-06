<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTbGiayXnTruongTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_giay_xn_truong', function (Blueprint $table) {
            $table->string('MaGiayXN_Truong', 20);
            $table->dateTime('NgayCap');
            $table->string('NamHoc', 20);
            $table->primary('MaGiayXN_Truong');
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
        Schema::dropIfExists('tb_giay_xn_truong');
    }
}
