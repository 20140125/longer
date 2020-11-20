<?php
namespace database;

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
            $table->text('content')->comment('消息体');
            $table->string('from_client_id', 64)->default('0')->comment('发送方');
            $table->string('to_client_id', 64)->default('0')->comment('接收方');
            $table->integer('room_id', 11)->default(0)->comment('房间号');
            $table->index('from_client_id');
            $table->index('to_client_id');
            $table->index('room_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
