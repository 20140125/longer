<?php
namespace database;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOsApiDocTable extends Migration
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
            $table->integer('type')->default(0)->comment('接口名称类型');
            $table->text('html')->comment('接口详情 (html)');
            $table->text('markdown')->comment('接口详情 (markdown)');
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
