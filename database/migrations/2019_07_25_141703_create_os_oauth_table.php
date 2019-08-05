<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOsOauthTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('os_oauth', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->index('openid');
            $table->index('oauth_type');
            $table->index('role_id');
            $table->string('username',64)->default('0')->comment('账号名');
            $table->string('openid',64)->default('0')->comment('第三方Openid');
            $table->string('access_token',64)->default('0')->comment('第三方账号token');
            $table->string('avatar_url',128)->default('0')->comment('用户头像');
            $table->string('url',128)->default('0')->comment('详细地址');
            $table->string('refresh_token',64)->default('0')->comment('刷新token');
            $table->string('oauth_type',16)->default('0')->comment('账户来源');
            $table->integer('role_id')->default(0)->comment('角色ID');
            $table->string('remember_token',32)->default('0')->comment('用户token');
            $table->integer('created_at')->default(0)->comment('授权时间');
            $table->integer('updated_at')->default(0)->comment('修改时间');
            $table->integer('expires')->default(0)->comment('access_token过期时间');
            $table->tinyInteger('status')->default(1)->comment('允许登录 1 是 2 否');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('os_oauth');
    }
}
