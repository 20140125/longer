<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatedOsChatTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('os_chat', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->char('from_client_id',20)->default('0')->comment('发送用户客户端ID');
            $table->string('from_client_name',32)->default('0')->comment('发送用户名称');
            $table->index('from_client_name');
            $table->char('to_client_id',20)->default('0')->comment('接收用户客户端ID');
            $table->string('to_client_name',32)->default('0')->comment('接收用户名称');
            $table->index('to_client_name');
            $table->string('content',512)->default('0')->comment('发送内容');
            $table->string('nsg_type',8)->default('text')->comment('消息类型');
            $table->string('avatar_url',128)->default('')->comment('发送者头像');
            $table->string('time',16)->default('0')->comment('发送时间');
            $table->string('type',8)->default('say')->comment('消息体');
            $table->string('room_id',8)->default('0')->comment('房间ID');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('os_chat');
    }
}
