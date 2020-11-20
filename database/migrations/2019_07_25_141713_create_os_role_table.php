<?php
namespace database;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOsRoleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('os_role', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('role_name')->default('0')->comment('角色名称');
            $table->text('auth_ids')->comment('权限ID');
            $table->text('auth_url')->comment('权限地址');
            $table->tinyInteger('status')->default(1)->comment('角色状态 1 启用 2 禁用');
            $table->integer('created_at')->default(0)->comment('创建时间');
            $table->integer('updated_at')->default(0)->comment('修改时间');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('os_role');
    }
}
