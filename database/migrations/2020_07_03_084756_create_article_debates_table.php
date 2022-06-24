<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArticleDebatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('article_debates', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('cid');
            $table->integer('aid'); //著作id
            $table->tinyInteger('type')->default(0); //来自：0协作1评审2讨论
            $table->integer('type_id'); //对应归口类型id
            $table->string('title');  //辩标题
            $table->integer('referee_id')->nullable();//裁判id
            $table->string('referee',20)->nullable();//裁判
            $table->timestamp('deadline');  //截止时间
            $table->integer('Aauthor_id');//攻方id
            $table->string('Aauthor',20);//攻方
            $table->integer('Bauthor_id');//辩方id
            $table->string('Bauthor',20);//辩方
            $table->integer('ARedstars')->default(0);//攻方红星
            $table->integer('ABlackstars')->default(0);//攻方黑星
            $table->integer('BRedstars')->default(0);//辩方红星
            $table->integer('BBlackstars')->default(0);//辩方黑星
            $table->integer('RRedstars')->default(0);//裁判红星
            $table->integer('RBlackstars')->default(0);//裁判黑星
            $table->text('AopeningStatement');//正方开篇陈词（含立论）
            $table->text('BopeningStatement')->nullable();//辩方开篇陈词（含立论）
            $table->timestamp('BOScreateTime')->nullable();
            $table->text('AfreeDebate') -> nullable();//正方自由辩论
            $table->timestamp('AFDcreateTime')->nullable();
            $table->text('BfreeDebate') -> nullable();//辩方自由辩论
            $table->timestamp('BFDcreateTime')->nullable();
            $table->text('AclosingStatement') -> nullable();//正方总结陈词
            $table->timestamp('ACScreateTime')->nullable();
            $table->text('BclosingStatement') -> nullable();//辩方总结陈词
            $table->timestamp('BCScreateTime')->nullable();
            $table->text('analyse') -> nullable();//裁判分析            
            $table->timestamp('analyseTime')->nullable();
            $table->text('summary') -> nullable();//裁判总结
            $table->tinyInteger('status')->default(0); //状态：0正在辩论，1正常结束，2正方放弃，3辩方放弃
            $table->tinyInteger('victory')->nullable(); //胜利方,1攻方2辩方
            $table->integer('views')->default(0);//浏览数
            $table->integer('heat')->default(0);//热度
            $table->text('remark');
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
        Schema::dropIfExists('article_debates');
    }
}
