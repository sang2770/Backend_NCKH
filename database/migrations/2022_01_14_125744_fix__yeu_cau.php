<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixYeuCau extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tb_yeucau', function (Blueprint $table) {
            $table->dateTime('NgayXuLy')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('Tb_yeucau', function (Blueprint $table) {
            $table->dateTime('NgayXuLy')->change();
        });
    }
}
