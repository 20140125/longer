<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOsApiLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('os_api_log', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->index('api_id');
            $table->string('username',64)->default('0')->comment('执行人');
            $table->integer('api_id')->default(0)->comment('接口ID');
            $table->string('desc')->default('0')->comment('操作记录');
            $table->integer('updated_at')->default(0)->nullable()->comment('操作时间');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('os_api_log');
    }
}
