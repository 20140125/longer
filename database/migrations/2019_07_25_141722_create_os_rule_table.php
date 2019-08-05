<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOsRuleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('os_rule', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name',64)->default('0')->comment('权限名称');
            $table->string('href',64)->default('0')->comment('权限地址');
            $table->integer('pid')->default(0)->comment('权限上级');
            $table->string('path')->default('0')->comment('权限路径');
            $table->string('level')->default('0')->comment('权限等级');
            $table->tinyInteger('status')->default(1)->comment('权限状态 1 开启 2 关闭');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('os_rule');
    }
}
