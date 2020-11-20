<?php
namespace database;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOsUserCenterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('os_user_center', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('uid')->default(0)->comment('用户ID');
            $table->string('u_name', 64)->default('0')->comment('用户名');
            $table->string('tags', 128)->default('0')->comment('标签');
            $table->string('local', 64)->default('0')->comment('居住地址');
            $table->string('ip_address', 32)->default('0')->comment('IP地址');
            $table->tinyInteger('notice_status')->default(1)->comment('介绍站内通知 1 是 2 否');
            $table->tinyInteger('user_status')->default(1)->comment('用户账号信息修改通知 1 是 2 否');
            $table->string('desc', 128)->default('0')->comment('自我介绍/座右铭');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('os_user_center');
    }
}
