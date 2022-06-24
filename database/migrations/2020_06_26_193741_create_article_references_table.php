<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArticleReferencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('article_references', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('type'); //引用文献类型1J期刊文章2M专著3C论文集4D学位论文5R研究报告6Z其他如法规条例7N报纸文章和网页文章（可含网址？）等
            $table->integer('part_id'); //所属著作分部id
            $table->unsignedTinyInteger('sort'); //排序
            $table->string('author');   //参考文献作者
            $table->string('title')->unique();
            $table->string('periodical');   //期刊名、毕业院校、出版社
            $table->string('publish',20);   //出版时间、发布日期+卷号、期号
            $table->string('pagenumber',20);   //页码范围
            $table->UnsignedMediumInteger('creator');   //创建者
            $table->UnsignedMediumInteger('revisor');   //更新者
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
        Schema::dropIfExists('article_references');
    }
}
