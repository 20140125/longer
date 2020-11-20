<?php
namespace database;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOsReqRuleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('os_req_rule', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('username', 64)->default('0')->comment('用户名称');
            $table->integer('user_id')->default(0)->comment('用户ID');
            $table->index('user_id');
            $table->string('href', 32)->default('0')->comment('请求授权地址');
            $table->tinyInteger('status')->default(2)->comment('是否批准 1 是 2 否');
            $table->index('status');
            $table->integer('created_at')->default(0)->comment('请求时间');
            $table->integer('updated_at')->nullable()->comment('授权时间');
            $table->integer('expires')->nullable()->comment('权限有限期');
            $table->index('expires');
            $table->string('desc', 128)->nullable()->comment('请求授权说明');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('os_req_rule');
    }
}
