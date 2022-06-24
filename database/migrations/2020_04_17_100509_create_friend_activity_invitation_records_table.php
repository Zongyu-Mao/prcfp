<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFriendActivityInvitationRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('friend_activity_invitation_records', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->string('username',20);
            $table->integer('invite_id');
            $table->string('invite_username',20);
            $table->string('remark'); //说明
            $table->string('subject'); //事由
            $table->tinyInteger('type');   //类型0百科词条1著作2试卷
            $table->integer('type_id');   //对应的协作id，目前只考虑协作邀请
            $table->tinyInteger('inviteResult');   //结果：0过程1同意2拒绝
            $table->text('invitationLink');   //活动链接
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
        Schema::dropIfExists('friend_activity_invitation_records');
    }
}
