<?php

namespace App\Notifications\Organization\Group\Member;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Home\Organization\Group;
use App\Home\Organization\Group\GroupUser;

class GroupMemberPositionChangedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(GroupUser $groupUser)
    {
        $this->groupUser = $groupUser;
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

    // 评审计划创建成功后对相关用户的通知
    public function toDatabase($notifiable)
    {
        $group = Group::find($this->groupUser->gid);
        $link = '/home/organization/groupDetail/'.$group->id.'/'.$group->title;
        return [
            'creator_id'    => $group->manage_id,
            'creator'       => $group->manager,
            'link'          => $link,
            'matter'        => '你在组织《'.$group->title.'》的成员身份已经变更。'
        ];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
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
