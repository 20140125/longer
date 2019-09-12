<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOsEmotionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('os_emotion', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->tinyInteger('type')->default(1)->comment('类型');
            $table->index('type');
            $table->string('title',64)->default('0')->comment('名称');
            $table->string('icon',256)->default('0')->comment('icon');
            $table->string('emoji',32)->default('0')->comment('emoji');
            $table->string('unified',32)->default('0')->comment('unified');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('os_emotion');
    }
}
