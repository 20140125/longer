<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOsPushTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('os_push', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('uid',32)->default('0')->comment('推送ID');
            $table->string('username',32)->default('0')->comment('推送姓名');
            $table->string('info',256)->default('0')->comment('推送信息');
            $table->string('state',8)->default('0')->comment('推送状态');
            $table->tinyInteger('status',3)->default(1)->comment('实时推送 1 是 2 否');
            $table->integer('created_at')->default(0)->comment('推送时间');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('os_push');
    }
}
