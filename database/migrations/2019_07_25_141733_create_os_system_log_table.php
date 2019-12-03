<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOsSystemLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('os_system_log', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->index('username');
            $table->index('ip_address');
            $table->string('username',32)->default('0')->comment('执行者');
            $table->string('url',64)->default('0')->comment('操作地址');
            $table->string('ip_address',31)->default('0')->comment('IP_Address');
            $table->string('log',512)->default('0')->comment('日志记录');
            $table->integer('created_at')->default(0)->comment('创建时间');
            $table->string('day')->default('0')->comment('当天日期');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('os_system_log');
    }
}
