<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOsUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('os_users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->index('username');
            $table->string('username',64)->default('0')->comment('用户名');
            $table->string('email',32)->default('0')->comment('邮箱');
            $table->integer('role_id')->default(0)->comment('角色ID');
            $table->string('ip_address',32)->default('127.0.0.1')->comment('ip');
            $table->tinyInteger('status',3)->default(1)->comment('用户状态 1 是 2 否');
            $table->integer('created_at',11)->default(0)->comment('注册时间');
            $table->integer('updated_at',11)->default(0)->comment('登录时间');
            $table->string('password',32)->default('0')->comment('密码');
            $table->string('salt',8)->default('0')->comment('盐值');
            $table->string('access_token',32)->default('0')->comment('登录token');
            $table->char('phone_number',11)->default('0')->comment('手机号码');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('os_users');
    }
}
