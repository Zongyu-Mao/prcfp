<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSurveillanceMarksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('surveillance_marks', function (Blueprint $table) {
            $table->id();//标记，在巡查、警示均无效之后或者巡视者认为不需要警示的情况下可以直接标记，标记的内容与主内容举报类似，直接进入本分类管理员组
            $table -> Integer('user_id');//巡查者
            $table -> tinyInteger('weight');//权重
            $table -> Integer('tcid');//分类id
            $table -> Integer('sid');//巡查对象id
            $table -> string('mark_ids');//标记的内容id集合（由于不走举报，因此独立再加一个类似medal的内容，可以参考medal）
            $table -> Integer('wid');//建议的处理方式，
            $table -> tinyInteger('status')->default(0);//巡查结果0未处理1通过2不通过，标记是由管理员组反馈的，在处理规则下，处理规则根据权重
            $table -> string('remark');//记录
            $table->timestamps(); //这里留下时间戳是为了改变status
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('surveillance_marks');
    }
}
