<?php

namespace App\Notifications\Personal\Relationship;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Home\Personal\Relationship\FriendActivityInvitationRecord;

class FriendActivityInvitationRepliedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(FriendActivityInvitationRecord $friendActivityInvitationRecord)
    {
        $this->friendActivityInvitationRecord = $friendActivityInvitationRecord;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    // 用户同意好友添加后对用户的通知
    public function toDatabase($notifiable)
    {
        $r = $this->friendActivityInvitationRecord;
        $user_id = $r->invite_id;
        $username = $r->invite_username;
        $subject = $r->subject;
        $replyResult = $r->inviteResult;
        if($replyResult == '1'){
            $reply = '同意';
        }elseif($replyResult == '2'){
            $reply = '暂时无心';
        }
        $link = $r->invitationLink;
        return [
            'creator_id'    => $user_id,
            'creator'       => $username,
            'link'          => $link,
            'matter'        => '好友'.$username.'['.$reply.']你的邀请：'.$subject.'。'
        ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
