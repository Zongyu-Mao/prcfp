<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArticleResortsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('article_resorts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('aid'); 
            $table->integer('cid'); //关联著作id
            $table->integer('pid'); //pid为0是求助文章，不为0是帮助文章
            $table->date('deadline');  //截止时间
            $table->string('title');  //求助内容标题，必填
            $table->text('content');  //求助内容，必填
            $table->integer('author_id');  //作者id
            $table->string('author',20);  //作者
            $table->tinyInteger('status') -> default(0);//状态:0正在解决1已经解决2没有应答并失效3有应答但失效4被举报或者状态:0待处理1采纳2拒绝3失效4举报
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
        Schema::dropIfExists('article_resorts');
    }
}
