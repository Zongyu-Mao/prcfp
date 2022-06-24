<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEntryResortsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('entry_resorts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('cid'); //fenlei
            $table->integer('eid'); //关联词条id
            $table->integer('pid'); //关联求助文章id
            $table->date('deadline');  //截止时间
            $table->string('title');  //求助内容标题，必填
            $table->text('content');  //求助内容，必填
            $table->integer('author_id');  //作者id
            $table->string('author',20);  //作者
            $table->tinyInteger('status') -> default(0);//状态:0正在解决1已经解决2没有应答并失效3有应答但失效4被举报
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('entry_resorts');
    }
}
