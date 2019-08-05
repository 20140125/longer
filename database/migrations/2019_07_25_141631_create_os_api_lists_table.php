<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOsApiListsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('os_api_lists', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->index('type');
            $table->string('desc',256)->default('0')->comment('接口描述');
            $table->integer('type')->default(0)->comment('接口名称类型');
            $table->string('href',256)->default('https://www.fanglonger.com')->comment('接口地址');
            $table->string('methods',8)->default('0')->comment('请求方法');
            $table->string('request',1024)->default('0')->comment('请求字段说明');
            $table->string('response',1024)->default('0')->comment('返回字段说明');
            $table->text('response_string')->comment('接口详情');
            $table->string('remark',256)->default('0')->comment('接口备注说明');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('os_api_lists');
    }
}
