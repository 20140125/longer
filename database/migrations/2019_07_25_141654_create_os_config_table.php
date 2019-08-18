<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOsConfigTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('os_config', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name',32)->default('0')->comment('配置名称');
            $table->string('value',2048)->default('[]')->nullable()->comment('配置值 (json)');
            $table->integer('created_at')->default(0)->comment('创建时间');
            $table->integer('updated_at')->default(0)->comment('修改时间');
            $table->tinyInteger('status')->default(1)->comment('配置状态 1 开启 2 关闭');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('os_config');
    }
}
