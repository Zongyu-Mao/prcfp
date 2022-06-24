<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table -> Integer('cid');     //所属分类
            $table -> string('title') -> unique();       //标题
            $table -> text('content') -> nullable();
            $table -> tinyInteger('status') -> default(0);      //文章状态,0正常1关闭
            $table -> Integer('creator_id');    //创建者
            $table -> Integer('lasteditor_id');      //最后编辑者
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
        Schema::dropIfExists('documents');
    }
}
