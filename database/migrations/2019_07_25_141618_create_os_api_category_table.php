<?php
namespace database;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOsApiCategoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('os_api_category', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->index('pid');
            $table->string('name', 64)->default('0')->comment('接口名称');
            $table->integer('pid')->default(0)->comment('上级ID');
            $table->string('path', 64)->default('0')->comment('接口路径');
            $table->tinyInteger('level')->default(0)->comment('接口层级');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('os_api_category');
    }
}
