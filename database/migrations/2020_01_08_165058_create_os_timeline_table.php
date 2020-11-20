<?php
namespace database;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOsTimelineTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('os_timeline', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('content', 64)->default('0')->comment('文案内容');
            $table->string('timestamp', 32)->default('0')->comment('时间点');
            $table->string('type', '16')->default('success')->comment('primary / success / warning / danger / info');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('os_timeline');
    }
}
