<?php
namespace database;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatedOsSendEmailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('os_send_email', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('email', 64)->default('0')->comment('邮箱');
            $table->index('email');
            $table->string('code', 8)->default('0')->comment('验证码');
            $table->index('code');
            $table->dateTime('created_at')->comment('创建时间');
            $table->dateTime('updated_at')->comment('修改时间');
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
